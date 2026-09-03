<?php

use App\Http\Controllers\BuktiPembayaranController;
use App\Http\Controllers\LaporanExportController;
use App\Models\Fasilitas;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome', [
    'fasilitas' => Fasilitas::aktif()->orderBy('nama')->get(),
]))->name('home');

Route::get('/fasilitas', fn () => view('public.fasilitas.index', [
    'fasilitas' => Fasilitas::aktif()->orderBy('nama')->get(),
]))->name('fasilitas.public');

Route::get('/fasilitas/{fasilitas}', fn (Fasilitas $fasilitas) => view('public.fasilitas.show', [
    'fasilitas' => $fasilitas,
    'fasilitasLain' => Fasilitas::aktif()->where('id', '!=', $fasilitas->id)->inRandomOrder()->take(3)->get(),
]))->name('fasilitas.detail');

Route::view('/panduan', 'public.panduan')->name('panduan');
Route::view('/tentang', 'public.tentang')->name('tentang');
Route::view('/kontak', 'public.kontak')->name('kontak');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'panel.dashboard.index')->name('dashboard');

    // Pengguna: jadwal + peminjaman + pembayaran.
    Route::middleware('role:pengguna')->group(function () {
        Route::livewire('jadwal', 'jadwal.index')->name('jadwal.index');
        Route::livewire('peminjaman/baru', 'peminjaman.create')->name('peminjaman.create');
        Route::livewire('peminjaman', 'peminjaman.index')->name('peminjaman.index');
        Route::livewire('peminjaman/{peminjaman}/bayar', 'pembayaran.show')->name('pembayaran.show');
    });

    // Verifikasi pembayaran: pengelola/admin.
    // Verifikasi pembayaran & Data transaksi: pengelola/admin.
    Route::middleware('role:admin,pengelola')->group(function () {
        Route::livewire('verifikasi', 'verifikasi.index')->name('verifikasi.index');
        Route::livewire('transaksi', 'panel.transaksi.index')->name('transaksi.index');
    });

    // Panel pengelola/admin: fasilitas, slot, blokir.
    Route::middleware('role:admin,pengelola')->prefix('panel')->group(function () {
        Route::livewire('fasilitas', 'panel.fasilitas.index')->name('panel.fasilitas.index');
        Route::livewire('fasilitas/baru', 'panel.fasilitas.form')->name('panel.fasilitas.create');
        Route::livewire('fasilitas/{fasilitas}/ubah', 'panel.fasilitas.form')->name('panel.fasilitas.edit');
        Route::livewire('slot-sesi', 'panel.slot-sesi.index')->name('panel.slot-sesi.index');
        Route::livewire('blokir-slot', 'panel.blokir-slot.index')->name('panel.blokir-slot.index');
    });

    // Panel admin: kelola pengguna & laporan.
    Route::middleware('role:admin')->group(function () {
        Route::livewire('panel/pengguna', 'panel.pengguna.index')->name('panel.pengguna.index');
        Route::livewire('laporan', 'panel.laporan.index')->name('laporan.index');
        Route::get('laporan/export', LaporanExportController::class)->name('laporan.export');
    });

    // Bukti pembayaran private (Hard Rule 4): pemilik + pengelola/admin.
    Route::get('bukti/{pembayaran}', BuktiPembayaranController::class)->name('bukti.show');
});

require __DIR__.'/settings.php';
