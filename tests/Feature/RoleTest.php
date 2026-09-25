<?php

use App\Models\User;

test('pengguna role cannot access panel fasilitas and panel pengguna', function () {
    $pengguna = User::factory()->pengguna()->create();
    $this->actingAs($pengguna);

    $this->get(route('panel.fasilitas.index'))->assertForbidden();
    $this->get(route('panel.pengguna.index'))->assertForbidden();
    $this->get(route('verifikasi.index'))->assertForbidden();
    $this->get(route('transaksi.index'))->assertForbidden();
    $this->get(route('laporan.index'))->assertForbidden();

    // Daftar peminjaman tetap dapat diakses pengguna (hanya miliknya).
    $this->get(route('peminjaman.index'))->assertOk();
});

test('pengelola can access verifikasi and transaksi but not pengguna', function () {
    $pengelola = User::factory()->pengelola()->create();
    $this->actingAs($pengelola);

    $this->get(route('verifikasi.index'))->assertOk();
    $this->get(route('transaksi.index'))->assertOk();
    $this->get(route('panel.fasilitas.index'))->assertOk();
    $this->get(route('panel.pengguna.index'))->assertForbidden();
    $this->get(route('laporan.index'))->assertForbidden();

    // Pengelola dapat melihat semua peminjaman.
    $this->get(route('peminjaman.index'))->assertOk();
});

test('admin can access panel pengguna and laporan and transaksi', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('panel.pengguna.index'))->assertOk();
    $this->get(route('laporan.index'))->assertOk();
    $this->get(route('transaksi.index'))->assertOk();
    $this->get(route('verifikasi.index'))->assertOk();

    // Admin dapat melihat semua peminjaman.
    $this->get(route('peminjaman.index'))->assertOk();
});

test('tamu diarahkan ke login saat membuka daftar peminjaman', function () {
    $this->get(route('peminjaman.index'))->assertRedirect(route('login'));
});
