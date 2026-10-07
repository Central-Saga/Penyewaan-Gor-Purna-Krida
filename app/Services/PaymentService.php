<?php

namespace App\Services;

use App\Mail\PembayaranDiverifikasiMail;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Simpan bukti pembayaran dan konfirmasi penyewaan secara OTOMATIS.
     * Tidak ada verifikasi manual: pembayaran langsung terverifikasi dan
     * peminjaman berstatus disetujui (jadwal tersewa).
     */
    public function upload(Peminjaman $peminjaman, UploadedFile $bukti, string $metode, ?User $aktor = null): Pembayaran
    {
        if ($peminjaman->status !== Peminjaman::MENUNGGU_PEMBAYARAN) {
            throw ValidationException::withMessages([
                'status' => __('Peminjaman tidak sedang menunggu pembayaran.'),
            ]);
        }

        $pembayaran = \DB::transaction(function () use ($peminjaman, $bukti, $metode, $aktor) {
            $pembayaran = Pembayaran::create([
                'peminjaman_id' => $peminjaman->id,
                'nominal' => $peminjaman->fasilitas->tarif_per_sesi,
                'metode' => $metode,
                'status' => Pembayaran::TERVERIFIKASI,
                'verified_at' => now(),
                'diverifikasi_oleh' => null, // sistem otomatis, bukan verifikator manusia
            ]);

            $pembayaran->addMedia($bukti->getRealPath())
                ->usingFileName($bukti->hashName())
                ->toMediaCollection('bukti', 'local');

            app(BookingService::class)->transisi(
                $peminjaman,
                Peminjaman::DISETUJUI,
                __('Bukti pembayaran diunggah (:metode) — penyewaan otomatis dikonfirmasi', ['metode' => $metode]),
                $aktor,
            );

            return $pembayaran;
        });

        // Notifikasi email di-queue setelah transaksi DB commit (driver log di dev).
        Mail::to($peminjaman->user->email)->queue(new PembayaranDiverifikasiMail($peminjaman->fresh()));

        return $pembayaran;
    }
}
