<?php

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Support\Facades\Artisan;

test('command release expired membatalkan peminjaman yang lewat 24 jam dan membebaskan slot', function () {
    $service = app(BookingService::class);
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $tanggal = today()->addDays(3)->toDateString();

    $peminjaman = $service->create($userA, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ]);

    // Set expired_at ke masa lalu
    $peminjaman->update(['expired_at' => now()->subHour()]);

    Artisan::call('peminjaman:release-expired');

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::DIBATALKAN);

    // Slot sekarang bisa dipesan user lain
    $peminjamanBaru = $service->create($userB, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ]);

    expect($peminjamanBaru->status)->toBe(Peminjaman::MENUNGGU_PEMBAYARAN);
});

test('command selesaikan mengubah peminjaman disetujui yang tanggal sewanya lewat menjadi selesai', function () {
    $service = app(BookingService::class);
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $user = User::factory()->create();

    $kemarin = today()->subDay()->toDateString();

    $peminjaman = $service->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $kemarin,
    ]);

    // Simulasikan disetujui
    $peminjaman->update(['status' => Peminjaman::DISETUJUI]);

    Artisan::call('peminjaman:selesaikan');

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::SELESAI);
});
