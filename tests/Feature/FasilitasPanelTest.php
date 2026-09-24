<?php

use App\Models\Fasilitas;
use App\Models\User;
use Livewire\Livewire;

test('pengguna tidak bisa mengakses panel fasilitas slot dan blokir', function () {
    $pengguna = User::factory()->pengguna()->create();
    $this->actingAs($pengguna);

    $this->get(route('panel.fasilitas.index'))->assertForbidden();
    $this->get(route('panel.fasilitas.create'))->assertForbidden();
    $this->get(route('panel.slot-sesi.index'))->assertForbidden();
    $this->get(route('panel.blokir-slot.index'))->assertForbidden();
});

test('pengelola bisa membuat fasilitas baru', function () {
    $pengelola = User::factory()->pengelola()->create();
    $this->actingAs($pengelola);

    Livewire::test('panel.fasilitas.form')
        ->set('nama', 'Lapangan Bulu Tangkis')
        ->set('jenis', 'indoor')
        ->set('deskripsi', 'Lapangan indoor standar BWF')
        ->set('kapasitas', 30)
        ->set('tarif', 75000)
        ->set('statusAktif', true)
        ->call('simpan')
        ->assertHasNoErrors()
        ->assertRedirect(route('panel.fasilitas.index'));

    $fasilitas = Fasilitas::where('nama', 'Lapangan Bulu Tangkis')->first();

    expect($fasilitas)->not->toBeNull();
    expect($fasilitas->jenis)->toBe('indoor');
    expect($fasilitas->kapasitas)->toBe(30);
    expect($fasilitas->tarif_per_sesi)->toBe(75000);
    expect((bool) $fasilitas->status_aktif)->toBeTrue();
});

test('pengelola bisa mengubah fasilitas yang ada', function () {
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create(['nama' => 'Lapangan Lama', 'tarif_per_sesi' => 50000]);
    $this->actingAs($pengelola);

    Livewire::test('panel.fasilitas.form', ['fasilitas' => $fasilitas])
        ->assertSet('nama', 'Lapangan Lama')
        ->set('nama', 'Lapangan Baru')
        ->set('tarif', 120000)
        ->call('simpan')
        ->assertHasNoErrors()
        ->assertRedirect(route('panel.fasilitas.index'));

    $fasilitas->refresh();
    expect($fasilitas->nama)->toBe('Lapangan Baru');
    expect($fasilitas->tarif_per_sesi)->toBe(120000);
});
