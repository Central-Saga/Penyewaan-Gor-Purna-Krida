<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\SlotSesi;
use Illuminate\Database\Seeder;

class SlotSesiSeeder extends Seeder
{
    /**
     * Slot sesi default per fasilitas: Pagi, Siang, Malam.
     */
    public function run(): void
    {
        $sesi = [
            ['nama' => 'Pagi', 'jam_mulai' => '08:00:00', 'jam_selesai' => '10:00:00'],
            ['nama' => 'Siang', 'jam_mulai' => '13:00:00', 'jam_selesai' => '15:00:00'],
            ['nama' => 'Malam', 'jam_mulai' => '19:00:00', 'jam_selesai' => '21:00:00'],
        ];

        foreach (Fasilitas::all() as $fasilitas) {
            foreach ($sesi as $sesiData) {
                SlotSesi::updateOrCreate(
                    ['fasilitas_id' => $fasilitas->id, 'nama' => $sesiData['nama']],
                    $sesiData,
                );
            }
        }
    }
}