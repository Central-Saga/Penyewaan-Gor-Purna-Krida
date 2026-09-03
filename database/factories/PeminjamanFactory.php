<?php

namespace Database\Factories;

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Peminjaman>
 */
class PeminjamanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => 'GOR-'.today()->format('Ymd').'-'.sprintf('%04d', fake()->unique()->numberBetween(0, 9999)),
            'user_id' => User::factory(),
            'fasilitas_id' => Fasilitas::factory(),
            'slot_sesi_id' => SlotSesi::factory(),
            'tanggal' => today()->addDay()->toDateString(),
            'status' => Peminjaman::MENUNGGU_PEMBAYARAN,
            'expired_at' => now()->addHours(24),
        ];
    }

    /**
     * Status menunggu verifikasi.
     */
    public function menungguVerifikasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Peminjaman::MENUNGGU_VERIFIKASI,
        ]);
    }

    /**
     * Status disetujui.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Peminjaman::DISETUJUI,
        ]);
    }

    /**
     * Status dibatalkan (tidak mengunci slot).
     */
    public function dibatalkan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Peminjaman::DIBATALKAN,
            'expired_at' => null,
        ]);
    }

    /**
     * Status selesai.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Peminjaman::SELESAI,
        ]);
    }

    /**
     * Tanggal lewat (untuk test auto-selesai).
     */
    public function tanggalLewat(): static
    {
        return $this->state(fn (array $attributes) => [
            'tanggal' => today()->subDay()->toDateString(),
        ]);
    }

    /**
     * Expired (untuk test auto-batal).
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expired_at' => now()->subMinutes(30),
        ]);
    }
}
