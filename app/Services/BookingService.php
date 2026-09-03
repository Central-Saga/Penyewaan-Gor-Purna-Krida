<?php

namespace App\Services;

use App\Models\BlokirSlot;
use App\Models\Peminjaman;
use App\Models\PeminjamanLog;
use App\Models\SlotSesi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
        Peminjaman::MENUNGGU_PEMBAYARAN => [
            Peminjaman::MENUNGGU_VERIFIKASI, // upload bukti
            Peminjaman::DIBATALKAN, // batal manual / expired
        ],
        Peminjaman::MENUNGGU_VERIFIKASI => [
            Peminjaman::DISETUJUI, // verifikasi setuju
            Peminjaman::MENUNGGU_PEMBAYARAN, // tolak bukti
            Peminjaman::DIBATALKAN, // batal
        ],
        Peminjaman::DISETUJUI => [
            Peminjaman::SELESAI, // tanggal lewat
        ],
    ];

    /**
     * Buat peminjaman baru dengan cek bentrok atomik.
     *
     * @param  array{fasilitas_id: int, slot_sesi_id: int, tanggal: string}  $data
     */
    public function create(User $user, array $data): Peminjaman
    {
        return DB::transaction(function () use ($user, $data) {
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
                'status' => Peminjaman::MENUNGGU_PEMBAYARAN,
                'expired_at' => now()->addHours(24),
            ]);

            PeminjamanLog::log(
                $peminjaman,
                null,
                Peminjaman::MENUNGGU_PEMBAYARAN,
                __('Booking dibuat'),
                $user,
            );

            return $peminjaman;
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
