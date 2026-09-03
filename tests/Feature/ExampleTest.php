<?php

use App\Models\Fasilitas;

test('returns a successful response for home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
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
