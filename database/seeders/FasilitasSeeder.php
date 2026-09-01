<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    /**
     * Data master fasilitas GOR Purnakrida dari pengelola.
     */
    public function run(): void
    {
        $fasilitas = [
            ['nama' => 'Badminton 1', 'jenis' => 'indoor', 'kapasitas' => 20, 'tarif_per_sesi' => 50000,
                'deskripsi' => 'Lapangan badminton indoor 1 dengan lantai mat synphonic dan pencahayaan memadai.'],
            ['nama' => 'Badminton 2', 'jenis' => 'indoor', 'kapasitas' => 20, 'tarif_per_sesi' => 50000,
                'deskripsi' => 'Lapangan badminton indoor 2 dengan lantai mat synphonic dan pencahayaan memadai.'],
            ['nama' => 'Basket', 'jenis' => 'indoor', 'kapasitas' => 60, 'tarif_per_sesi' => 150000,
                'deskripsi' => 'Lapangan basket indoor bertaraf standar, cocok untuk latihan dan pertandingan.'],
            ['nama' => 'Volley', 'jenis' => 'outdoor', 'kapasitas' => 40, 'tarif_per_sesi' => 100000,
                'deskripsi' => 'Lapangan volley outdoor dengan net standar dan area penonton.'],
            ['nama' => 'Tenis Meja', 'jenis' => 'indoor', 'kapasitas' => 12, 'tarif_per_sesi' => 50000,
                'deskripsi' => 'Ruang tenis meja dengan 4 meja standar nasional.'],
        ];

        foreach ($fasilitas as $data) {
            Fasilitas::updateOrCreate(['nama' => $data['nama']], $data);
        }
    }
}