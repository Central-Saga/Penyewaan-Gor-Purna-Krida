<?php

use App\Mail\PembayaranDiverifikasiMail;
use App\Models\Fasilitas;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Helper: buat peminjaman berstatus menunggu_pembayaran via pengajuan + persetujuan.
 */
function peminjamanSiapBayar(User $user, Fasilitas $fasilitas, SlotSesi $slot, ?User $pengelola = null): Peminjaman
{
    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->addDays(2)->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    app(BookingService::class)->setujuiPengajuan($peminjaman, $pengelola ?? User::factory()->pengelola()->create());

    return $peminjaman->fresh();
}

test('upload bukti bayar otomatis mengonfirmasi penyewaan (disetujui) dan menyimpan media', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = peminjamanSiapBayar($user, $fasilitas, $slot);

    $file = UploadedFile::fake()->image('bukti.jpg', 600, 600)->size(1500); // 1.5MB
    $pembayaran = app(PaymentService::class)->upload($peminjaman, $file, 'transfer', $user);

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::DISETUJUI);
    expect($pembayaran->status)->toBe(Pembayaran::TERVERIFIKASI);
    expect($pembayaran->verified_at)->not->toBeNull();
    expect($pembayaran->diverifikasi_oleh)->toBeNull();
    expect($pembayaran->getFirstMedia('bukti'))->not->toBeNull();
});

test('upload bukti mengirim email konfirmasi dan mengunci slot', function () {
    Storage::fake('local');
    Mail::fake();

    $user = User::factory()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $tanggal = today()->addDays(2)->toDateString();

    $peminjaman = peminjamanSiapBayar($user, $fasilitas, $slot);

    app(PaymentService::class)->upload($peminjaman, UploadedFile::fake()->image('bukti.jpg'), 'transfer', $user);

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::DISETUJUI);

    Mail::assertQueued(PembayaranDiverifikasiMail::class, function (PembayaranDiverifikasiMail $mail) use ($user): bool {
        return $mail->hasTo($user->email);
    });

    expect((new PembayaranDiverifikasiMail($peminjaman->fresh()))->render())->toContain($peminjaman->kode);

    // Booking kedua pada slot sama ditolak (slot terkunci karena disetujui).
    $userLain = User::factory()->create();
    expect(fn () => app(BookingService::class)->create($userLain, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100)))->toThrow(ValidationException::class);
});

test('upload bukti ditolak bila peminjaman belum menunggu pembayaran', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    // Peminjaman masih menunggu_verifikasi (belum disetujui pengelola).
    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->addDays(3)->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    expect(fn () => app(PaymentService::class)->upload($peminjaman, UploadedFile::fake()->image('bukti.jpg'), 'transfer', $user))
        ->toThrow(ValidationException::class);
});

test('akses bukti pembayaran dibatasi untuk pemilik dan pengelola/admin saja', function () {
    Storage::fake('local');

    $pemilik = User::factory()->create();
    $userLain = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = peminjamanSiapBayar($pemilik, $fasilitas, $slot);
    $pembayaran = app(PaymentService::class)->upload($peminjaman, UploadedFile::fake()->image('bukti.jpg'), 'transfer', $pemilik);

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

test('akses surat peminjaman dibatasi untuk pemilik dan pengelola/admin saja', function () {
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
    ], UploadedFile::fake()->create('surat.pdf', 100));

    // User lain 403
    $this->actingAs($userLain);
    $this->get(route('surat.show', $peminjaman))->assertForbidden();

    // Pemilik 200
    $this->actingAs($pemilik);
    $this->get(route('surat.show', $peminjaman))->assertOk();

    // Pengelola 200
    $this->actingAs($pengelola);
    $this->get(route('surat.show', $peminjaman))->assertOk();
});

test('halaman pembayaran menampilkan rekening resmi dari konfigurasi', function () {
    $user = User::factory()->pengguna()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = peminjamanSiapBayar($user, $fasilitas, $slot);

    $this->actingAs($user)
        ->get(route('pembayaran.show', $peminjaman))
        ->assertOk()
        ->assertSee(config('gor.rekening.bank'))
        ->assertSee(config('gor.rekening.nomor'));
});
