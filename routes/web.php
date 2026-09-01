<?php

use App\Models\Fasilitas;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome', [
    'fasilitas' => Fasilitas::aktif()->orderBy('nama')->get(),
]))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // Panel pengelola/admin: fasilitas, slot, blokir.
    Route::middleware('role:admin,pengelola')->prefix('panel')->group(function () {
        Route::livewire('fasilitas', 'panel.fasilitas.index')->name('panel.fasilitas.index');
        Route::livewire('fasilitas/baru', 'panel.fasilitas.form')->name('panel.fasilitas.create');
        Route::livewire('fasilitas/{fasilitas}/ubah', 'panel.fasilitas.form')->name('panel.fasilitas.edit');
        Route::livewire('slot-sesi', 'panel.slot-sesi.index')->name('panel.slot-sesi.index');
        Route::livewire('blokir-slot', 'panel.blokir-slot.index')->name('panel.blokir-slot.index');
    });

    // Panel admin: kelola pengguna.
    Route::middleware('role:admin')->prefix('panel')->group(function () {
        Route::livewire('pengguna', 'panel.pengguna.index')->name('panel.pengguna.index');
    });
});

require __DIR__.'/settings.php';