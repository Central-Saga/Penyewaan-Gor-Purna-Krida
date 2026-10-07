<?php

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

test('command release expired membatalkan peminjaman menunggu pembayaran yang lewat 24 jam dan membebaskan slot', function () {
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
    ], UploadedFile::fake()->create('surat.pdf', 100));

    // Pengajuan disetujui → menunggu_pembayaran + deadline 24 jam.
    $service->setujuiPengajuan($peminjaman, User::factory()->pengelola()->create());

    // Set expired_at ke masa lalu
    $peminjaman->update(['expired_at' => now()->subHour()]);

    Artisan::call('peminjaman:release-expired');

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::DIBATALKAN);

    // Slot sekarang bisa dipesan user lain
    $peminjamanBaru = $service->create($userB, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));

    expect($peminjamanBaru->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
});

test('command release expired membatalkan peminjaman ditolak yang lewat batas revisi 24 jam', function () {
    $service = app(BookingService::class);
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $user = User::factory()->create();
    $tanggal = today()->addDays(3)->toDateString();

    $peminjaman = $service->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));

    $service->tolakPengajuan($peminjaman, 'Surat belum ditandatangani', User::factory()->pengelola()->create());

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::DITOLAK);

    // Lewati batas revisi.
    $peminjaman->update(['expired_at' => now()->subHour()]);

    Artisan::call('peminjaman:release-expired');

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::DIBATALKAN);
    expect($peminjaman->fresh()->getRawOriginal('status_aktif'))->toBeNull();

    // Slot lepas → user lain bisa booking.
    $kedua = $service->create(User::factory()->create(), [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));

    expect($kedua->id)->not->toBe($peminjaman->id);
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
    ], UploadedFile::fake()->create('surat.pdf', 100));

    // Simulasikan disetujui
    $peminjaman->update(['status' => Peminjaman::DISETUJUI]);

    Artisan::call('peminjaman:selesaikan');

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::SELESAI);
});
