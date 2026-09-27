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
    ]);
    $file = UploadedFile::fake()->image('bukti.jpg');
    $pembayaran = app(PaymentService::class)->upload(
        $peminjaman,
        $file,
        'transfer',
        $pengguna
    );

    app(PaymentService::class)->verifikasi($pembayaran, true, null, $pengelola);

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
    $admin = User::factory()->admin()->create();

    // Create a facility
    $fasilitas = Fasilitas::factory()->create(['nama' => 'Badminton 1']);

    // Create an approved booking for this facility
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $user = User::factory()->create();

    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ]);

    $this->actingAs($admin);
    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee($fasilitas->nama);
});

test('pengelola dashboard menampilkan okupansi lapangan hari ini', function () {
    $pengelola = User::factory()->pengelola()->create();

    $fasilitas = Fasilitas::factory()->create(['nama' => 'Volley Ball']);
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $user = User::factory()->create();

    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ]);

    $this->actingAs($pengelola);
    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertSee($fasilitas->nama);
});
