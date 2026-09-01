<?php

namespace Database\Factories;

use App\Models\Fasilitas;
use App\Models\SlotSesi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SlotSesi>
 */
class SlotSesiFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fasilitas_id' => Fasilitas::factory(),
            'nama' => fake()->randomElement(['Pagi', 'Siang', 'Malam']),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ];
    }

    /**
     * Slot pagi 08:00–10:00.
     */
    public function pagi(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Pagi',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
        ]);
    }

    /**
     * Slot siang 13:00–15:00.
     */
    public function siang(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Siang',
            'jam_mulai' => '13:00:00',
            'jam_selesai' => '15:00:00',
        ]);
    }

    /**
     * Slot malam 19:00–21:00.
     */
    public function malam(): static
    {
        return $this->state(fn (array $attributes) => [
            'nama' => 'Malam',
            'jam_mulai' => '19:00:00',
            'jam_selesai' => '21:00:00',
        ]);
    }
}