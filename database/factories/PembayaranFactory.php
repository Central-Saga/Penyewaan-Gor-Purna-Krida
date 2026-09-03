<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peminjaman_id' => Peminjaman::factory(),
            'nominal' => 50000,
            'metode' => fake()->randomElement(['transfer', 'qris']),
            'status' => Pembayaran::MENUNGGU_VERIFIKASI,
        ];
    }

    /**
     * Pembayaran terverifikasi.
     */
    public function terverifikasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Pembayaran::TERVERIFIKASI,
        ]);
    }

    /**
     * Pembayaran ditolak.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Pembayaran::DITOLAK,
            'catatan_verifikasi' => 'Bukti tidak jelas',
        ]);
    }
}
