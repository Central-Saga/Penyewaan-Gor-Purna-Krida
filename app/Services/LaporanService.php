<?php

namespace App\Services;

use App\Models\Fasilitas;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Illuminate\Support\Collection;

class LaporanService
{
    /**
     * Get per-facility statistics including total bookings, active bookings, and verified income.
     *
     * @return Collection<int, Fasilitas>
     */
    public function statistikFasilitas(?string $mulai = null, ?string $sampai = null): Collection
    {
        return Fasilitas::query()
            ->withCount(['peminjaman as total_peminjaman' => function ($q) use ($mulai, $sampai) {
                if ($mulai) {
                    $q->whereDate('tanggal', '>=', $mulai);
                }
                if ($sampai) {
                    $q->whereDate('tanggal', '<=', $sampai);
                }
            }])
            ->withCount(['peminjaman as peminjaman_aktif' => function ($q) use ($mulai, $sampai) {
                if ($mulai) {
                    $q->whereDate('tanggal', '>=', $mulai);
                }
                if ($sampai) {
                    $q->whereDate('tanggal', '<=', $sampai);
                }
                $q->whereIn('status', Peminjaman::STATUS_AKTIF);
            }])
            ->with(['media'])
            ->orderBy('nama')
            ->get()
            ->map(function (Fasilitas $facility) use ($mulai, $sampai): Fasilitas {
                // Calculate verified income for this facility
                $incomeQuery = Pembayaran::where('status', Pembayaran::TERVERIFIKASI);

                if ($mulai) {
                    $incomeQuery->whereHas('peminjaman', function ($q) use ($mulai) {
                        $q->whereDate('tanggal', '>=', $mulai);
                    });
                }

                if ($sampai) {
                    $incomeQuery->whereHas('peminjaman', function ($q) use ($sampai) {
                        $q->whereDate('tanggal', '<=', $sampai);
                    });
                }

                $incomeQuery->whereHas('peminjaman', fn ($q) => $q->where('fasilitas_id', $facility->id));

                // Atribut terhitung (bukan kolom DB) untuk konsumsi view dashboard.
                $facility->setAttribute('pendapatan_terverifikasi', $incomeQuery->sum('nominal'));

                return $facility;
            });
    }

    /**
     * Data agregat peminjaman berdasarkan periode tanggal.
     *
     * @return array{
     *     perFasilitas: array<string, int>,
     *     perStatus: array<string, int>,
     *     daftar: Collection<int, Peminjaman>
     * }
     */
    public function peminjaman(string $mulai, string $sampai): array
    {
        $daftar = Peminjaman::query()
            ->with(['fasilitas', 'slotSesi', 'user'])
            ->whereBetween('tanggal', [$mulai, $sampai])
            ->orderBy('tanggal')
            ->get();

        $perFasilitas = [];
        foreach (Fasilitas::orderBy('nama')->get() as $f) {
            $perFasilitas[$f->nama] = $daftar->where('fasilitas_id', $f->id)->count();
        }

        $perStatus = [
            Peminjaman::MENUNGGU_PEMBAYARAN => $daftar->where('status', Peminjaman::MENUNGGU_PEMBAYARAN)->count(),
            Peminjaman::MENUNGGU_VERIFIKASI => $daftar->where('status', Peminjaman::MENUNGGU_VERIFIKASI)->count(),
            Peminjaman::DISETUJUI => $daftar->where('status', Peminjaman::DISETUJUI)->count(),
            Peminjaman::DIBATALKAN => $daftar->where('status', Peminjaman::DIBATALKAN)->count(),
            Peminjaman::SELESAI => $daftar->where('status', Peminjaman::SELESAI)->count(),
        ];

        return [
            'perFasilitas' => $perFasilitas,
            'perStatus' => $perStatus,
            'daftar' => $daftar,
        ];
    }

    /**
     * Data transaksi pembayaran terverifikasi dalam periode verified_at (AC5).
     *
     * @return Collection<int, Pembayaran>
     */
    public function pemasukan(string $mulai, string $sampai): Collection
    {
        return Pembayaran::query()
            ->with(['peminjaman.fasilitas', 'peminjaman.user', 'peminjaman.slotSesi', 'verifikator'])
            ->where('status', Pembayaran::TERVERIFIKASI)
            ->whereDate('verified_at', '>=', $mulai)
            ->whereDate('verified_at', '<=', $sampai)
            ->orderBy('verified_at')
            ->get();
    }
}
