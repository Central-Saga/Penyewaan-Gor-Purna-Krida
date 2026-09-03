<?php

namespace Database\Factories;

use App\Models\Fasilitas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fasilitas>
 */
class FasilitasFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Lapangan '.fake()->word(),
            'jenis' => fake()->randomElement(['indoor', 'outdoor']),
            'deskripsi' => fake()->sentence(),
            'kapasitas' => fake()->numberBetween(10, 100),
            'tarif_per_sesi' => fake()->randomElement([50000, 75000, 100000, 150000]),
            'status_aktif' => true,
        ];
    }

    /**
     * Fasilitas nonaktif (tidak tampil di daftar publik).
     */
    public function nonaktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_aktif' => false,
        ]);
    }

    /**
     * Fasilitas outdoor.
     */
    public function outdoor(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'outdoor',
        ]);
    }

    /**
     * Fasilitas indoor.
     */
    public function indoor(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'indoor',
        ]);
    }
}
