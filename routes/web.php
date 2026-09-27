<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BuktiPembayaranController;
use App\Http\Controllers\KontakController;
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
Route::post('/kontak', [KontakController::class, 'store'])->middleware('throttle:kontak')->name('kontak.store');

Route::get('/sitemap.xml', function () {
    $hariIni = now()->toAtomString();

    $statis = [
        ['loc' => route('home'), 'lastmod' => $hariIni],
        ['loc' => route('fasilitas.public'), 'lastmod' => $hariIni],
        ['loc' => route('panduan'), 'lastmod' => $hariIni],
        ['loc' => route('tentang'), 'lastmod' => $hariIni],
        ['loc' => route('kontak'), 'lastmod' => $hariIni],
    ];

    $detail = Fasilitas::query()
        ->aktif()
        ->orderBy('nama')
        ->get()
        ->map(fn (Fasilitas $fasilitas): array => [
            'loc' => route('fasilitas.detail', $fasilitas),
            'lastmod' => $fasilitas->updated_at?->toAtomString() ?? $hariIni,
        ])
        ->all();

    $urls = array_merge($statis, $detail);

    $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

    foreach ($urls as $url) {
        $xml .= '  <url>'."\n";
        $xml .= '    <loc>'.e($url['loc']).'</loc>'."\n";
        $xml .= '    <lastmod>'.e($url['lastmod']).'</lastmod>'."\n";
        $xml .= '</url>'."\n";
    }

    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap.xml');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'panel.dashboard.index')->name('dashboard');

    // Daftar peminjaman: pengguna melihat miliknya, admin/pengelola melihat semua
    Route::livewire('peminjaman', 'peminjaman.index')->name('peminjaman.index');

    // Pengguna: jadwal + peminjaman + pembayaran.
    Route::middleware('role:pengguna')->group(function () {
        Route::livewire('jadwal', 'jadwal.index')->name('jadwal.index');
        Route::livewire('peminjaman/baru', 'peminjaman.create')->name('peminjaman.create');
        Route::livewire('peminjaman/{peminjaman}/bayar', 'pembayaran.show')->name('pembayaran.show');
    });

    // Verifikasi pembayaran: pengelola/admin.
    Route::middleware('role:admin,pengelola')->group(function () {
        Route::livewire('verifikasi', 'verifikasi.index')->name('verifikasi.index');
        Route::livewire('transaksi', 'panel.transaksi.index')->name('transaksi.index');
    });

    // Panel pengelola/admin: fasilitas, slot, blokir, activity logs.
    Route::middleware('role:admin,pengelola')->prefix('panel')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs.index');
        Route::get('activity-logs/export', [ActivityLogController::class, 'export'])->name('activity.logs.export');
        Route::get('activity-logs/{log}', [ActivityLogController::class, 'show'])->name('activity.logs.show');

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
