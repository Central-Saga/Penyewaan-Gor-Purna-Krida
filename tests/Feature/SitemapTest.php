<?php

use App\Models\Fasilitas;

test('sitemap.xml mengembalikan xml valid berisi url fasilitas', function () {
    $fasilitas = Fasilitas::factory()->create(['nama' => 'Badminton 1', 'status_aktif' => true]);
    Fasilitas::factory()->create(['nama' => 'Nonaktif', 'status_aktif' => false]);

    $response = $this->get(route('sitemap.xml'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml');

    $response->assertSee('<urlset', false);
    $response->assertSee('<loc>'.route('home').'</loc>', false);
    $response->assertSee('<loc>'.route('fasilitas.public').'</loc>', false);
    $response->assertSee('<loc>'.route('fasilitas.detail', $fasilitas).'</loc>', false);

    // Pastikan XML dapat diparse.
    $xml = simplexml_load_string($response->getContent());
    expect($xml)->not->toBeFalse();
});

test('sitemap.xml hanya memuat fasilitas aktif', function () {
    $aktif = Fasilitas::factory()->create(['status_aktif' => true]);
    $nonaktif = Fasilitas::factory()->create(['status_aktif' => false]);

    $response = $this->get(route('sitemap.xml'));

    $response->assertSee(route('fasilitas.detail', $aktif), false);
    $response->assertDontSee(route('fasilitas.detail', $nonaktif), false);
});
