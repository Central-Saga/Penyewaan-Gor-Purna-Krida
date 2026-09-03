<?php

use App\Models\User;

test('pengguna role cannot access panel fasilitas and panel pengguna', function () {
    $pengguna = User::factory()->create();
    $this->actingAs($pengguna);

    $this->get(route('panel.fasilitas.index'))->assertForbidden();
    $this->get(route('panel.pengguna.index'))->assertForbidden();
    $this->get(route('verifikasi.index'))->assertForbidden();
    $this->get(route('transaksi.index'))->assertForbidden();
    $this->get(route('laporan.index'))->assertForbidden();
});

test('pengelola can access verifikasi and transaksi but not pengguna', function () {
    $pengelola = User::factory()->pengelola()->create();
    $this->actingAs($pengelola);

    $this->get(route('verifikasi.index'))->assertOk();
    $this->get(route('transaksi.index'))->assertOk();
    $this->get(route('panel.fasilitas.index'))->assertOk();
    $this->get(route('panel.pengguna.index'))->assertForbidden();
    $this->get(route('laporan.index'))->assertForbidden();
});

test('admin can access panel pengguna and laporan and transaksi', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $this->get(route('panel.pengguna.index'))->assertOk();
    $this->get(route('laporan.index'))->assertOk();
    $this->get(route('transaksi.index'))->assertOk();
    $this->get(route('verifikasi.index'))->assertOk();
});
