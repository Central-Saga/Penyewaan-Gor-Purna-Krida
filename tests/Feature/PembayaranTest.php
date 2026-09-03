<?php

use App\Models\Fasilitas;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

test('upload bukti bayar mengubah status menjadi menunggu_verifikasi dan menyimpan media', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->addDays(2)->toDateString(),
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg', 600, 600)->size(1500); // 1.5MB
    $pembayaran = app(PaymentService::class)->upload($peminjaman, $file, 'transfer', $user);

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
    expect($pembayaran->status)->toBe(Pembayaran::MENUNGGU_VERIFIKASI);
    expect($pembayaran->getFirstMedia('bukti'))->not->toBeNull();
});

test('verifikasi approve mengubah status menjadi disetujui dan slot terkunci', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $tanggal = today()->addDays(2)->toDateString();

    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ]);
    $file = UploadedFile::fake()->image('bukti.jpg');
    $pembayaran = app(PaymentService::class)->upload($peminjaman, $file, 'transfer', $user);

    app(PaymentService::class)->verifikasi($pembayaran, true, null, $pengelola);

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::DISETUJUI);
    expect($pembayaran->fresh()->status)->toBe(Pembayaran::TERVERIFIKASI);
    expect($pembayaran->fresh()->diverifikasi_oleh)->toBe($pengelola->id);

    // Booking kedua pada slot sama ditolak
    $userLain = User::factory()->create();
    expect(fn () => app(BookingService::class)->create($userLain, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ]))->toThrow(ValidationException::class);
});

test('verifikasi tolak wajib catatan dan membebaskan user mengupload ulang', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->addDays(3)->toDateString(),
    ]);
    $file = UploadedFile::fake()->image('bukti.jpg');
    $pembayaran = app(PaymentService::class)->upload($peminjaman, $file, 'transfer', $user);

    // Tolak tanpa catatan throws exception
    expect(fn () => app(PaymentService::class)->verifikasi($pembayaran, false, null, $pengelola))
        ->toThrow(ValidationException::class);

    // Tolak dengan catatan berhasil
    app(PaymentService::class)->verifikasi($pembayaran, false, 'Nominal transfer kurang', $pengelola);

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::MENUNGGU_PEMBAYARAN);
    expect($pembayaran->fresh()->status)->toBe(Pembayaran::DITOLAK);
});

test('akses bukti pembayaran dibatasi untuk pemilik dan pengelola/admin saja', function () {
    Storage::fake('local');

    $pemilik = User::factory()->create();
    $userLain = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = app(BookingService::class)->create($pemilik, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->addDays(2)->toDateString(),
    ]);
    $file = UploadedFile::fake()->image('bukti.jpg');
    $pembayaran = app(PaymentService::class)->upload($peminjaman, $file, 'transfer', $pemilik);

    // User lain 403
    $this->actingAs($userLain);
    $this->get(route('bukti.show', $pembayaran))->assertForbidden();

    // Pemilik 200
    $this->actingAs($pemilik);
    $this->get(route('bukti.show', $pembayaran))->assertOk();

    // Pengelola 200
    $this->actingAs($pengelola);
    $this->get(route('bukti.show', $pembayaran))->assertOk();
});
