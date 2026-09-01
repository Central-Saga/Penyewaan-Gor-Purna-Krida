<?php

namespace Database\Factories;

use App\Models\BlokirSlot;
use App\Models\Fasilitas;
use App\Models\SlotSesi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlokirSlot>
 */
class BlokirSlotFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fasilitas_id' => Fasilitas::factory(),
            'slot_sesi_id' => SlotSesi::factory(),
            'tanggal' => today()->toDateString(),
            'alasan' => fake()->randomElement(['Perawatan lapangan', 'Kegiatan internal', 'Turnamen']),
            'diblokir_oleh' => User::factory()->pengelola(),
        ];
    }
}