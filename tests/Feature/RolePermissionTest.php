<?php

use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

test('admin dapat membuka halaman role & permission', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('panel.role.index'))
        ->assertOk()
        ->assertSee('kelola_fasilitas')
        ->assertSee('Role & Permission');
});

test('pengelola dan pengguna tidak dapat membuka halaman role & permission', function () {
    $pengelola = User::factory()->pengelola()->create();
    $pengguna = User::factory()->pengguna()->create();

    $this->actingAs($pengelola)->get(route('panel.role.index'))->assertForbidden();
    $this->actingAs($pengguna)->get(route('panel.role.index'))->assertForbidden();
});

test('admin dapat memberi dan mencabut permission pada role pengelola', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    // Awalnya pengelola tidak punya lihat_laporan.
    expect(Role::findByName('pengelola')->hasPermissionTo('lihat_laporan'))->toBeFalse();

    Livewire::test('panel.role.index')
        ->call('togglePermission', 'pengelola', 'lihat_laporan')
        ->assertHasNoErrors();

    expect(Role::findByName('pengelola')->fresh()->hasPermissionTo('lihat_laporan'))->toBeTrue();

    // Cabut kembali.
    Livewire::test('panel.role.index')
        ->call('togglePermission', 'pengelola', 'lihat_laporan')
        ->assertHasNoErrors();

    expect(Role::findByName('pengelola')->fresh()->hasPermissionTo('lihat_laporan'))->toBeFalse();
});

test('permission wajib role admin (kelola_pengguna) tidak dapat dicabut', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test('panel.role.index')
        ->call('togglePermission', 'admin', 'kelola_pengguna')
        ->assertHasErrors('permission');

    expect(Role::findByName('admin')->fresh()->hasPermissionTo('kelola_pengguna'))->toBeTrue();
});
