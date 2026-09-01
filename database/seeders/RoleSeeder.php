<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Role + permission matrix WORKFLOWS §A6b.
     */
    public function run(): void
    {
        $permissions = [
            'kelola_pengguna',
            'kelola_fasilitas',
            'kelola_jadwal',
            'verifikasi_peminjaman',
            'lihat_transaksi',
            'lihat_laporan',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $pengguna = Role::findOrCreate('pengguna', 'web');
        $pengelola = Role::findOrCreate('pengelola', 'web');
        $admin = Role::findOrCreate('admin', 'web');

        $pengguna->syncPermissions([]);

        $pengelola->syncPermissions([
            'kelola_fasilitas',
            'kelola_jadwal',
            'verifikasi_peminjaman',
            'lihat_transaksi',
        ]);

        $admin->syncPermissions($permissions);
    }
}