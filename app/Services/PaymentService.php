<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Simpan bukti pembayaran dan naikkan peminjaman ke menunggu_verifikasi.
     */
    public function upload(Peminjaman $peminjaman, UploadedFile $bukti, string $metode, ?User $aktor = null): Pembayaran
    {
        if ($peminjaman->status !== Peminjaman::MENUNGGU_PEMBAYARAN) {
            throw ValidationException::withMessages([
                'status' => __('Peminjaman tidak sedang menunggu pembayaran.'),
            ]);
        }

        return \DB::transaction(function () use ($peminjaman, $bukti, $metode, $aktor) {
            $pembayaran = Pembayaran::create([
                'peminjaman_id' => $peminjaman->id,
                'nominal' => $peminjaman->fasilitas->tarif_per_sesi,
                'metode' => $metode,
                'status' => Pembayaran::MENUNGGU_VERIFIKASI,
            ]);

            $pembayaran->addMedia($bukti->getRealPath())
                ->usingFileName($bukti->hashName())
                ->toMediaCollection('bukti', 'local');

            app(BookingService::class)->transisi(
                $peminjaman,
                Peminjaman::MENUNGGU_VERIFIKASI,
                __('Bukti pembayaran diunggah (:metode)', ['metode' => $metode]),
                $aktor,
            );

            return $pembayaran;
        });
    }

    /**
     * Verifikasi pembayaran oleh pengelola/admin.
     * Setuju → peminjaman disetujui. Tolak → kembali ke menunggu_pembayaran,
     * bukti lama soft delete.
     */
    public function verifikasi(Pembayaran $pembayaran, bool $setuju, ?string $catatan, User $verifikator): void
    {
        \DB::transaction(function () use ($pembayaran, $setuju, $catatan, $verifikator) {
            $peminjaman = $pembayaran->peminjaman;

            if ($peminjaman->status !== Peminjaman::MENUNGGU_VERIFIKASI) {
                throw ValidationException::withMessages([
                    'status' => __('Peminjaman tidak sedang menunggu verifikasi.'),
                ]);
            }

            if ($setuju) {
                $pembayaran->update([
                    'status' => Pembayaran::TERVERIFIKASI,
                    'verified_at' => now(),
                    'diverifikasi_oleh' => $verifikator->id,
                ]);

                app(BookingService::class)->transisi(
                    $pembayaran->peminjaman,
                    Peminjaman::DISETUJUI,
                    __('Pembayaran disetujui'),
                    $verifikator,
                );

                return;
            }

            if (filled($catatan) === false) {
                throw ValidationException::withMessages([
                    'catatan_verifikasi' => __('Catatan wajib diisi saat menolak pembayaran.'),
                ]);
            }

            $pembayaran->update([
                'status' => Pembayaran::DITOLAK,
                'catatan_verifikasi' => $catatan,
                'diverifikasi_oleh' => $verifikator->id,
                'verified_at' => now(),
            ]);

            $pembayaran->delete(); // soft delete bukti lama

            app(BookingService::class)->transisi(
                $pembayaran->peminjaman,
                Peminjaman::MENUNGGU_PEMBAYARAN,
                __('Bukti ditolak: :catatan', ['catatan' => $catatan]),
                $verifikator,
            );
        });
    }
}
