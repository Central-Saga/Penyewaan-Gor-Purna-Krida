<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Akun admin + pengelola awal dari variabel lingkungan.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('seeder.admin_email', env('ADMIN_EMAIL', 'admin@gorpurnakrida.test'))],
            [
                'name' => 'Admin GOR Purnakrida',
                'no_hp' => null,
                'password' => env('ADMIN_PASSWORD', 'password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        )->assignRole('admin');

        User::updateOrCreate(
            ['email' => env('PENGELOLA_EMAIL', 'pengelola@gorpurnakrida.test')],
            [
                'name' => 'Pengelola GOR Purnakrida',
                'no_hp' => null,
                'password' => env('PENGELOLA_PASSWORD', 'password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        )->assignRole('pengelola');
    }
}
