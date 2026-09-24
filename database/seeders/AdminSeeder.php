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
            ['email' => config('seeder.admin_email')],
            [
                'name' => 'Admin GOR Purnakrida',
                'no_hp' => null,
                'password' => config('seeder.admin_password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        )->assignRole('admin');

        User::updateOrCreate(
            ['email' => config('seeder.pengelola_email')],
            [
                'name' => 'Pengelola GOR Purnakrida',
                'no_hp' => null,
                'password' => config('seeder.pengelola_password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        )->assignRole('pengelola');
    }
}
