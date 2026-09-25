<?php

use App\Models\Fasilitas;

test('returns a successful response for home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('halaman publik memuat meta SEO dasar', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('name="description"', false);
    $response->assertSee('rel="canonical"', false);
    $response->assertSee('property="og:title"', false);
    $response->assertSee('property="og:description"', false);
    $response->assertSee('property="og:url"', false);
    $response->assertSee('property="og:locale" content="id_ID"', false);
});

test('returns a successful response for fasilitas catalog page', function () {
    $response = $this->get(route('fasilitas.public'));

    $response->assertOk();
});

test('returns a successful response for fasilitas detail page', function () {
    $fasilitas = Fasilitas::factory()->create();

    $response = $this->get(route('fasilitas.detail', $fasilitas));

    $response->assertOk();
});

test('returns a successful response for panduan page', function () {
    $response = $this->get(route('panduan'));

    $response->assertOk();
});

test('returns a successful response for tentang page', function () {
    $response = $this->get(route('tentang'));

    $response->assertOk();
});

test('returns a successful response for kontak page', function () {
    $response = $this->get(route('kontak'));

    $response->assertOk();
});

test('panduan menampilkan rekening resmi dari konfigurasi', function () {
    $response = $this->get(route('panduan'));

    $response->assertOk();
    $response->assertSee(config('gor.rekening.bank'));
    $response->assertSee(config('gor.rekening.nomor'));
    $response->assertSee(config('gor.rekening.atas_nama'));
});
