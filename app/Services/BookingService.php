<?php

namespace App\Services;

use App\Mail\PeminjamanDibuatMail;
use App\Mail\PengajuanDisetujuiMail;
use App\Mail\PengajuanDitolakMail;
use App\Models\BlokirSlot;
use App\Models\Peminjaman;
use App\Models\PeminjamanLog;
use App\Models\SlotSesi;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Satu-satunya jalur tulis peminjaman (Hard Rule 2).
 * Anti-double-booking + state machine.
 */
class BookingService
{
    /**
     * Transisi status valid (WORKFLOWS §B1).
     *
     * @var array<string, list<string>>
     */
    private const TRANSISI_VALID = [
        Peminjaman::MENUNGGU_VERIFIKASI => [
            Peminjaman::MENUNGGU_PEMBAYARAN, // pengajuan disetujui pengelola
            Peminjaman::DITOLAK, // pengajuan perlu revisi
            Peminjaman::DIBATALKAN, // batal
        ],
        Peminjaman::DITOLAK => [
            Peminjaman::MENUNGGU_VERIFIKASI, // pengajuan direvisi & diajukan ulang
            Peminjaman::DIBATALKAN, // batal / kadaluarsa revisi
        ],
        Peminjaman::MENUNGGU_PEMBAYARAN => [
            Peminjaman::DISETUJUI, // bukti pembayaran diunggah (otomatis)
            Peminjaman::DIBATALKAN, // batal manual / expired
        ],
        Peminjaman::DISETUJUI => [
            Peminjaman::SELESAI, // tanggal lewat
        ],
    ];

    /**
     * Buat pengajuan peminjaman baru dengan surat resmi + cek bentrok atomik.
     *
     * @param  array{fasilitas_id: int, slot_sesi_id: int, tanggal: string}  $data
     */
    public function create(User $user, array $data, UploadedFile $surat): Peminjaman
    {
        $peminjaman = DB::transaction(function () use ($user, $data, $surat) {
            $slot = SlotSesi::query()
                ->whereKey($data['slot_sesi_id'])
                ->where('fasilitas_id', $data['fasilitas_id'])
                ->lockForUpdate()
                ->first();

            if ($slot === null) {
                throw ValidationException::withMessages([
                    'slot_sesi_id' => __('Slot tidak ditemukan untuk fasilitas ini.'),
                ]);
            }

            $bentrok = Peminjaman::query()
                ->where('fasilitas_id', $data['fasilitas_id'])
                ->whereDate('tanggal', $data['tanggal'])
                ->where('slot_sesi_id', $data['slot_sesi_id'])
                ->whereIn('status', Peminjaman::STATUS_AKTIF)
                ->lockForUpdate()
                ->exists();

            if ($bentrok) {
                throw ValidationException::withMessages([
                    'slot_sesi_id' => __('Slot sudah dipesan. Pilih slot atau tanggal lain.'),
                ]);
            }

            $diblokir = BlokirSlot::query()
                ->where('fasilitas_id', $data['fasilitas_id'])
                ->where('slot_sesi_id', $data['slot_sesi_id'])
                ->whereDate('tanggal', $data['tanggal'])
                ->exists();

            if ($diblokir) {
                throw ValidationException::withMessages([
                    'slot_sesi_id' => __('Slot diblokir pada tanggal tersebut.'),
                ]);
            }

            $peminjaman = Peminjaman::query()->create([
                'kode' => $this->generateKode($data['tanggal']),
                'user_id' => $user->id,
                'fasilitas_id' => $data['fasilitas_id'],
                'slot_sesi_id' => $data['slot_sesi_id'],
                'tanggal' => $data['tanggal'],
                'status' => Peminjaman::MENUNGGU_VERIFIKASI,
                'expired_at' => null,
            ]);

            // Hard Rule 4: surat resmi instansi disimpan private di disk local.
            $peminjaman->addMedia($surat->getRealPath())
                ->usingFileName($surat->hashName())
                ->toMediaCollection('surat_peminjaman', 'local');

            PeminjamanLog::log(
                $peminjaman,
                null,
                Peminjaman::MENUNGGU_VERIFIKASI,
                __('Pengajuan dibuat dengan surat resmi'),
                $user,
            );

            return $peminjaman;
        });

        // Notifikasi email di-queue setelah transaksi DB commit agar kegagalan
        // mail tidak membatalkan peminjaman yang sudah tersimpan.
        Mail::to($user->email)->queue(new PeminjamanDibuatMail($peminjaman));

        return $peminjaman;
    }

    /**
     * Setujui pengajuan pengelola → penyewa diminta melakukan pembayaran.
     * Transisi otomatis menetapkan deadline pembayaran 24 jam.
     */
    public function setujuiPengajuan(Peminjaman $peminjaman, User $pengelola): void
    {
        $this->transisi(
            $peminjaman,
            Peminjaman::MENUNGGU_PEMBAYARAN,
            __('Pengajuan disetujui pengelola'),
            $pengelola,
        );

        Mail::to($peminjaman->user->email)->queue(new PengajuanDisetujuiMail($peminjaman->fresh()));
    }

    /**
     * Tolak pengajuan → penyewa wajib merevisi (slot tetap terkunci 24 jam).
     */
    public function tolakPengajuan(Peminjaman $peminjaman, string $catatan, User $pengelola): void
    {
        if (filled($catatan) === false) {
            throw ValidationException::withMessages([
                'catatan' => __('Catatan wajib diisi saat menolak pengajuan.'),
            ]);
        }

        DB::transaction(function () use ($peminjaman, $catatan, $pengelola) {
            $peminjaman->update([
                'catatan_verifikasi' => $catatan,
                'expired_at' => now()->addHours(24),
            ]);

            $this->transisi(
                $peminjaman,
                Peminjaman::DITOLAK,
                __('Pengajuan ditolak: :catatan', ['catatan' => $catatan]),
                $pengelola,
            );
        });

        Mail::to($peminjaman->user->email)->queue(new PengajuanDitolakMail($peminjaman->fresh(), $catatan));
    }

    /**
     * Ajukan ulang pengajuan yang ditolak dengan jadwal/surat baru.
     * Fasilitas tidak berubah (ganti fasilitas = batalkan lalu buat pengajuan baru).
     *
     * @param  array{slot_sesi_id: int, tanggal: string}  $data
     */
    public function revisiPengajuan(Peminjaman $peminjaman, array $data, UploadedFile $surat, User $user): void
    {
        DB::transaction(function () use ($peminjaman, $data, $surat, $user) {
            $peminjaman->refresh();

            if ($peminjaman->status !== Peminjaman::DITOLAK) {
                throw ValidationException::withMessages([
                    'status' => __('Peminjaman tidak berstatus perlu revisi.'),
                ]);
            }

            $slot = SlotSesi::query()
                ->whereKey($data['slot_sesi_id'])
                ->where('fasilitas_id', $peminjaman->fasilitas_id)
                ->lockForUpdate()
                ->first();

            if ($slot === null) {
                throw ValidationException::withMessages([
                    'slot_sesi_id' => __('Slot tidak ditemukan untuk fasilitas ini.'),
                ]);
            }

            // Kecualikan record sendiri agar jadwal yang sama tetap boleh dipertahankan.
            $bentrok = Peminjaman::query()
                ->where('fasilitas_id', $peminjaman->fasilitas_id)
                ->whereDate('tanggal', $data['tanggal'])
                ->where('slot_sesi_id', $data['slot_sesi_id'])
                ->where('id', '!=', $peminjaman->id)
                ->whereIn('status', Peminjaman::STATUS_AKTIF)
                ->lockForUpdate()
                ->exists();

            if ($bentrok) {
                throw ValidationException::withMessages([
                    'slot_sesi_id' => __('Slot sudah dipesan. Pilih slot atau tanggal lain.'),
                ]);
            }

            $diblokir = BlokirSlot::query()
                ->where('fasilitas_id', $peminjaman->fasilitas_id)
                ->where('slot_sesi_id', $data['slot_sesi_id'])
                ->whereDate('tanggal', $data['tanggal'])
                ->exists();

            if ($diblokir) {
                throw ValidationException::withMessages([
                    'slot_sesi_id' => __('Slot diblokir pada tanggal tersebut.'),
                ]);
            }

            $peminjaman->update([
                'slot_sesi_id' => $data['slot_sesi_id'],
                'tanggal' => $data['tanggal'],
                'catatan_verifikasi' => null,
                'expired_at' => null,
            ]);

            $peminjaman->clearMediaCollection('surat_peminjaman');
            $peminjaman->addMedia($surat->getRealPath())
                ->usingFileName($surat->hashName())
                ->toMediaCollection('surat_peminjaman', 'local');

            $this->transisi(
                $peminjaman,
                Peminjaman::MENUNGGU_VERIFIKASI,
                __('Pengajuan direvisi dan diajukan ulang'),
                $user,
            );
        });
    }

    /**
     * Transisi status peminjaman dengan validasi + log.
     */
    public function transisi(Peminjaman $peminjaman, string $ke, ?string $catatan = null, ?User $aktor = null, string $aktorPeran = 'cron'): void
    {
        DB::transaction(function () use ($peminjaman, $ke, $catatan, $aktor, $aktorPeran) {
            $peminjaman->refresh();

            $dari = $peminjaman->status;

            $valid = self::TRANSISI_VALID[$dari] ?? [];

            if (! in_array($ke, $valid, true)) {
                throw ValidationException::withMessages([
                    'status' => __('Transisi status :dari ke :ke tidak valid.', [
                        'dari' => $dari,
                        'ke' => $ke,
                    ]),
                ]);
            }

            $peminjaman->update(['status' => $ke]);

            if ($ke === Peminjaman::MENUNGGU_PEMBAYARAN) {
                $peminjaman->update(['expired_at' => now()->addHours(24)]);
            }

            PeminjamanLog::log($peminjaman, $dari, $ke, $catatan, $aktor, $aktorPeran);
        });
    }

    /**
     * Generate kode unik GOR-YYYYMMDD-XXXX.
     */
    private function generateKode(string $tanggal): string
    {
        $prefix = 'GOR-'.str_replace('-', '', $tanggal).'-';

        do {
            $kode = $prefix.sprintf('%04d', random_int(0, 9999));
        } while (Peminjaman::query()->where('kode', $kode)->exists());

        return $kode;
    }
}
