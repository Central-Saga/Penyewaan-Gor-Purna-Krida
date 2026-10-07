<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;

/**
 * Data demo agar dashboard, laporan, dan tren pemasukan tidak kosong saat
 * dipresentasikan. Aman dijalankan berulang: hanya mengisi bila tabel
 * peminjaman masih kosong.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Peminjaman::query()->exists()) {
            return;
        }

        $fasilitasList = Fasilitas::query()->with('slotSesi')->orderBy('nama')->get();

        if ($fasilitasList->isEmpty()) {
            return;
        }

        $penyewa = collect([
            ['name' => 'Komunitas Badminton Badung', 'email' => 'komunitas.badminton@example.com'],
            ['name' => 'SMA Negeri 1 Kuta Utara', 'email' => 'sman1.kutautara@example.com'],
            ['name' => 'Klub Basket Purnakrida', 'email' => 'klub.basket@example.com'],
            ['name' => 'Komunitas Voli Pantai', 'email' => 'voli.pantai@example.com'],
        ])->map(function (array $data): User {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'no_hp' => '0812'.random_int(10000000, 99999999),
                    'email_verified_at' => now(),
                    'password' => 'password',
                ],
            );

            if (! $user->hasRole('pengguna')) {
                $user->assignRole('pengguna');
            }

            return $user;
        });

        $terpakai = [];
        $urutan = 0;

        // 6 bulan terakhir termasuk bulan berjalan.
        foreach (range(5, 0) as $mundur) {
            $bulan = today()->copy()->subMonthsNoOverflow($mundur);
            $bulanBerjalan = $bulan->isSameMonth(today());

            $hariList = [3, 8, 13, 18, 23, 27];

            // Pastikan ada jadwal hari ini agar dashboard "hari ini" terisi.
            if ($bulanBerjalan) {
                $hariList[] = today()->day;
                sort($hariList);
            }

            foreach ($hariList as $hari) {
                $tanggal = $bulan->copy()->day(min($hari, $bulan->daysInMonth));

                $fasilitas = $fasilitasList[$urutan % $fasilitasList->count()];
                $slot = $fasilitas->slotSesi[$urutan % max($fasilitas->slotSesi->count(), 1)] ?? null;

                if ($slot === null) {
                    continue;
                }

                $kunci = $fasilitas->id.'|'.$tanggal->toDateString().'|'.$slot->id;

                if (isset($terpakai[$kunci])) {
                    $urutan++;

                    continue;
                }

                $terpakai[$kunci] = true;
                $user = $penyewa[$urutan % $penyewa->count()];

                $status = $this->tentukanStatus($bulanBerjalan, $tanggal);
                $dibuat = $tanggal->copy()->subDays(2)->setTime(9, 0);

                // created_at tidak boleh di masa depan (mis. pengajuan untuk tanggal mendatang).
                if ($dibuat->isAfter(now())) {
                    $dibuat = now()->subHours(random_int(2, 20));
                }

                $peminjaman = new Peminjaman([
                    'kode' => 'GOR-'.$tanggal->format('Ymd').'-'.sprintf('%04d', 1000 + $urutan),
                    'user_id' => $user->id,
                    'fasilitas_id' => $fasilitas->id,
                    'slot_sesi_id' => $slot->id,
                    'tanggal' => $tanggal->toDateString(),
                    'status' => $status,
                    'catatan_verifikasi' => $status === Peminjaman::DITOLAK
                        ? 'Surat belum bertanda tangan dan cap instansi, mohon direvisi.'
                        : null,
                    'expired_at' => in_array($status, [Peminjaman::MENUNGGU_PEMBAYARAN, Peminjaman::DITOLAK], true)
                        ? $tanggal->copy()->endOfDay()
                        : null,
                ]);
                // created_at/updated_at tidak fillable → set langsung tanpa auto-timestamp.
                $peminjaman->timestamps = false;
                $peminjaman->created_at = $dibuat;
                $peminjaman->updated_at = $dibuat;
                $peminjaman->save();

                // Pembayaran terverifikasi untuk peminjaman yang sudah tersewa/selesai.
                if (in_array($status, [Peminjaman::DISETUJUI, Peminjaman::SELESAI], true)) {
                    // Booking hari ini diverifikasi hari ini agar "pemasukan hari ini" terisi.
                    $diverifikasi = $tanggal->isToday()
                        ? now()->subMinutes(random_int(30, 240))
                        : $tanggal->copy()->subDay()->setTime(10, 0);

                    $pembayaran = new Pembayaran([
                        'peminjaman_id' => $peminjaman->id,
                        'nominal' => $fasilitas->tarif_per_sesi,
                        'metode' => $urutan % 2 === 0 ? 'transfer' : 'qris',
                        'status' => Pembayaran::TERVERIFIKASI,
                        'verified_at' => $diverifikasi,
                    ]);
                    $pembayaran->timestamps = false;
                    $pembayaran->created_at = $dibuat;
                    $pembayaran->updated_at = $diverifikasi;
                    $pembayaran->save();
                }

                $urutan++;
            }
        }
    }

    /**
     * Sebar status realistis: bulan lampau selesai, bulan berjalan campuran
     * (hari ini tersewa, lampau selesai/batal, mendatang menunggu proses).
     */
    private function tentukanStatus(bool $bulanBerjalan, CarbonInterface $tanggal): string
    {
        if (! $bulanBerjalan) {
            return Peminjaman::SELESAI;
        }

        if ($tanggal->isToday()) {
            return Peminjaman::DISETUJUI;
        }

        if ($tanggal->isPast()) {
            return $tanggal->day % 3 === 0 ? Peminjaman::SELESAI : Peminjaman::DIBATALKAN;
        }

        // Tanggal mendatang: campuran menunggu verifikasi / perlu revisi / menunggu pembayaran.
        return match ($tanggal->day % 3) {
            0 => Peminjaman::MENUNGGU_VERIFIKASI,
            1 => Peminjaman::DITOLAK,
            default => Peminjaman::MENUNGGU_PEMBAYARAN,
        };
    }
}
