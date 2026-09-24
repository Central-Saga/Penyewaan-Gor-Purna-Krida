<?php

use App\Models\User;
use Livewire\Livewire;

test('admin tidak bisa menghapus akun sendiri', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    Livewire::test('panel.pengguna.index')
        ->call('hapus', $admin->id)
        ->assertHasErrors('hapus');

    expect(User::find($admin->id))->not->toBeNull();
    expect($admin->fresh()->hasRole('admin'))->toBeTrue();
});

test('admin tidak bisa menghapus atau demote admin terakhir', function () {
    $admin = User::factory()->admin()->create();
    $adminKedua = User::factory()->admin()->create();
    $this->actingAs($admin);

    // Dua admin: menghapus admin lain masih diperbolehkan.
    Livewire::test('panel.pengguna.index')
        ->call('hapus', $adminKedua->id)
        ->assertHasNoErrors('hapus');

    expect(User::find($adminKedua->id))->toBeNull();
    expect(User::role('admin')->count())->toBe(1);

    // Tinggal satu admin: hapus diri sendiri ditolak.
    Livewire::test('panel.pengguna.index')
        ->call('hapus', $admin->id)
        ->assertHasErrors('hapus');

    expect(User::find($admin->id))->not->toBeNull();

    // Tinggal satu admin: demote diri sendiri ditolak.
    Livewire::test('panel.pengguna.index')
        ->call('setRole', $admin->id, 'pengguna')
        ->assertHasErrors('role');

    expect($admin->fresh()->hasRole('admin'))->toBeTrue();
    expect(User::role('admin')->count())->toBe(1);
});
