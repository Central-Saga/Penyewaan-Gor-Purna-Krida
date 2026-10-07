<?php

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('dashboard menampilkan ringkasan data sesuai peran', function () {
    Storage::fake('local');

    $pengguna = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $admin = User::factory()->admin()->create();

    $fasilitas = Fasilitas::factory()->create(['tarif_per_sesi' => 150000]);
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = app(BookingService::class)->create($pengguna, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    app(BookingService::class)->setujuiPengajuan($peminjaman, $pengelola);

    $file = UploadedFile::fake()->image('bukti.jpg');
    app(PaymentService::class)->upload(
        $peminjaman->fresh(),
        $file,
        'transfer',
        $pengguna
    );

    // Pengguna melihat total peminjaman miliknya dan kode booking
    $this->actingAs($pengguna);
    $responsePengguna = $this->get(route('dashboard'));
    $responsePengguna->assertOk();
    $responsePengguna->assertSee($peminjaman->kode);
    // Regresi `nama_sesi`: nama sesi (bukan kolom tak ada) harus tampil.
    $responsePengguna->assertSee($slot->nama);

    // Pengelola melihat pemasukan hari ini
    $this->actingAs($pengelola);
    $responsePengelola = $this->get(route('dashboard'));
    $responsePengelola->assertOk();
    $responsePengelola->assertSee('150.000');

    // Admin melihat total peminjaman dan pengguna
    $this->actingAs($admin);
    $responseAdmin = $this->get(route('dashboard'));
    $responseAdmin->assertOk();
    $responseAdmin->assertSee('150.000');
});

test('pesan flash status tampil sebagai toast di panel', function () {
    $pengguna = User::factory()->create();

    $this->actingAs($pengguna)
        ->withSession(['status' => 'Peminjaman berhasil dibatalkan.'])
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Peminjaman berhasil dibatalkan.');
});

test('admin dashboard menampilkan statistik per lapangan', function () {
    Storage::fake('local');

    $admin = User::factory()->admin()->create();

    // Create a facility
    $fasilitas = Fasilitas::factory()->create(['nama' => 'Badminton 1']);

    // Create an approved booking for this facility
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $peminjaman = app(BookingService::class)->create(User::factory()->create(), [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    $this->actingAs($admin);
    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee($fasilitas->nama);
});

test('pengelola dashboard menampilkan okupansi lapangan hari ini', function () {
    Storage::fake('local');

    $pengelola = User::factory()->pengelola()->create();

    $fasilitas = Fasilitas::factory()->create(['nama' => 'Volley Ball']);
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $peminjaman = app(BookingService::class)->create(User::factory()->create(), [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    $this->actingAs($pengelola);
    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee($fasilitas->nama);
});

test('dashboard admin menampilkan tren, okupansi, dan aktivitas terbaru', function () {
    Storage::fake('local');

    $admin = User::factory()->admin()->create();

    $fasilitas = Fasilitas::factory()->create(['nama' => 'Basket Indoor']);
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    // Booking tersewa hari ini dengan pembayaran terverifikasi hari ini.
    $peminjaman = app(BookingService::class)->create(User::factory()->create(), [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    app(BookingService::class)->setujuiPengajuan($peminjaman, User::factory()->pengelola()->create());
    app(PaymentService::class)->upload($peminjaman->fresh(), UploadedFile::fake()->image('bukti.jpg'), 'transfer');

    $this->actingAs($admin);
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Tren Pemasukan 6 Bulan')
        ->assertSee('Okupansi Lapangan Hari Ini')
        ->assertSee('Aktivitas Terbaru')
        ->assertSee('Pemasukan Hari Ini')
        ->assertSee('Pemasukan Bulan Ini')
        ->assertSee($fasilitas->nama);
});

test('sidebar admin menampilkan menu operasional dan administrasi', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Verifikasi Pengajuan')
        ->assertSee('Fasilitas Lapangan')
        ->assertSee('Slot & Blokir Jadwal')
        ->assertSee('Log Aktivitas')
        ->assertSee('Role & Permission')
        ->assertSee('Kelola Pengguna');
});

test('sidebar pengelola tidak menampilkan menu khusus admin', function () {
    $pengelola = User::factory()->pengelola()->create();

    $this->actingAs($pengelola)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Verifikasi Pengajuan')
        ->assertSee('Fasilitas Lapangan')
        ->assertDontSee('Role & Permission')
        ->assertDontSee('Kelola Pengguna');
});
