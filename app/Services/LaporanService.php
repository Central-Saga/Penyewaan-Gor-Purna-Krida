<?php

namespace App\Services;

use App\Models\Fasilitas;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Collection;

class LaporanService
{
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
