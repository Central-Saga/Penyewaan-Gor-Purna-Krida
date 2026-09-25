<?php

use App\Mail\KontakMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

test('formulir kontak valid mengirim email ke sekretariat', function () {
    Mail::fake();

    $response = $this->from(route('kontak'))->post(route('kontak.store'), [
        'nama' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'subjek' => 'Pertanyaan Ketersediaan Fasilitas',
        'pesan' => 'Apakah lapangan badminton tersedia Sabtu depan?',
    ]);

    $response->assertRedirect(route('kontak'));
    $response->assertSessionHas('status');

    Mail::assertSent(KontakMessage::class, function (KontakMessage $mail): bool {
        return $mail->nama === 'Budi Santoso'
            && $mail->email === 'budi@example.com'
            && $mail->hasTo(config('mail.from.address'));
    });
});

test('formulir kontak menolak input tidak valid', function () {
    Mail::fake();

    $response = $this->from(route('kontak'))->post(route('kontak.store'), [
        'nama' => '',
        'email' => 'bukan-email',
        'pesan' => '',
    ]);

    $response->assertSessionHasErrors(['nama', 'email', 'pesan']);
    Mail::assertNothingSent();
});

test('formulir kontak dibatasi rate limit per ip', function () {
    Mail::fake();

    $payload = [
        'nama' => 'Spammer',
        'email' => 'spam@example.com',
        'pesan' => 'Pesan berulang',
    ];

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('kontak.store'), $payload)->assertRedirect();
    }

    $this->post(route('kontak.store'), $payload)->assertStatus(429);

    RateLimiter::clear('kontak:127.0.0.1');
});
