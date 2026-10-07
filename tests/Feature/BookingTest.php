<?php

use App\Mail\PeminjamanDibuatMail;
use App\Models\BlokirSlot;
use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\PeminjamanLog;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Storage::fake('local');
});

test('peminjaman dibuat mengirim email notifikasi ke pengguna', function () {
    Mail::fake();

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $user = User::factory()->create(['email' => 'penyewa@example.com']);

    $peminjaman = app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->addDays(3)->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    Mail::assertQueued(PeminjamanDibuatMail::class, function (PeminjamanDibuatMail $mail) use ($peminjaman, $user): bool {
        return $mail->peminjaman->is($peminjaman) && $mail->hasTo($user->email);
    });

    // Pastikan view email benar-benar bisa dirender (tidak ada error blade).
    $html = (new PeminjamanDibuatMail($peminjaman->fresh()))->render();
    expect($html)->toContain($peminjaman->kode);
});

test('test_dua_booking_slot_sama_yang_kedua_ditolak', function () {
    $service = app(BookingService::class);

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $tanggal = today()->addDays(3)->toDateString();

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Booking pertama berhasil.
    $pertama = $service->create($userA, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));

    expect($pertama->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);

    // Booking kedua pada slot sama ditolak.
    try {
        $service->create($userB, [
            'fasilitas_id' => $fasilitas->id,
            'slot_sesi_id' => $slot->id,
            'tanggal' => $tanggal,
        ], UploadedFile::fake()->create('surat.pdf', 100));

        $this->fail('Booking kedua seharusnya ditolak.');
    } catch (ValidationException $e) {
        expect($e->errors()['slot_sesi_id'][0])->toContain('Slot sudah dipesan');
    }

    expect(Peminjaman::count())->toBe(1);
});

test('test_booking_pada_slot_diblokir_ditolak', function () {
    $service = app(BookingService::class);

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->siang()->create();
    $pengelola = User::factory()->pengelola()->create();

    $tanggal = today()->addDays(5)->toDateString();

    BlokirSlot::create([
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
        'alasan' => 'Perawatan lapangan',
        'diblokir_oleh' => $pengelola->id,
    ]);

    $user = User::factory()->create();

    try {
        $service->create($user, [
            'fasilitas_id' => $fasilitas->id,
            'slot_sesi_id' => $slot->id,
            'tanggal' => $tanggal,
        ], UploadedFile::fake()->create('surat.pdf', 100));

        $this->fail('Booking pada slot diblokir seharusnya ditolak.');
    } catch (ValidationException $e) {
        expect($e->errors()['slot_sesi_id'][0])->toContain('diblokir');
    }
});

test('test_booking_dibatalkan_melepas_slot', function () {
    $service = app(BookingService::class);

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->malam()->create();

    $user = User::factory()->create();
    $tanggal = today()->addDays(2)->toDateString();

    $peminjaman = $service->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));

    // Batal → generated column NULL → slot lepas.
    $service->transisi($peminjaman, Peminjaman::DIBATALKAN, 'Batal manual', $user);

    $peminjaman->refresh();
    expect($peminjaman->status)->toBe(Peminjaman::DIBATALKAN);
    expect($peminjaman->getRawOriginal('status_aktif'))->toBeNull();

    // User lain bisa booking slot yang sama.
    $kedua = $service->create(User::factory()->create(), [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));

    expect($kedua->id)->not->toBe($peminjaman->id);
});

test('test_transisi_invalid_ditolak', function () {
    $service = app(BookingService::class);

    $peminjaman = Peminjaman::factory()->create([
        'status' => Peminjaman::DIBATALKAN,
    ]);

    try {
        $service->transisi($peminjaman, Peminjaman::DISETUJUI);
        $this->fail('Transisi dari dibatalkan seharusnya ditolak.');
    } catch (ValidationException $e) {
        expect($e->errors()['status'][0])->toContain('tidak valid');
    }
});

test('test_log_tercatat_setiap_transisi', function () {
    $service = app(BookingService::class);
    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->create();

    $peminjaman = $service->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->addDay()->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    $service->setujuiPengajuan($peminjaman, $pengelola);

    expect(PeminjamanLog::where('peminjaman_id', $peminjaman->id)->count())->toBe(2);
    expect(PeminjamanLog::where('peminjaman_id', $peminjaman->id)->latest('id')->first()->ke_status)
        ->toBe(Peminjaman::MENUNGGU_PEMBAYARAN);
});

test('test_kode_peminjaman_format_gor_yyyymmdd_xxxx', function () {
    $service = app(BookingService::class);
    $user = User::factory()->create();
    $tanggal = today()->addDays(4)->toDateString();

    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->create();

    $peminjaman = $service->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));

    expect($peminjaman->kode)->toMatch('/^GOR-\d{8}-\d{4}$/');
    // Pengajuan menunggu verifikasi pengelola tanpa deadline otomatis.
    expect($peminjaman->expired_at)->toBeNull();
});

test('halaman sewa fasilitas menampilkan tombol tata cara dan unduh template surat', function () {
    $pengguna = User::factory()->pengguna()->create();
    Fasilitas::factory()->create();

    $this->actingAs($pengguna)
        ->get(route('jadwal.index'))
        ->assertOk()
        ->assertSee('Tata Cara Menyewa')
        ->assertSee('Unduh Template Surat')
        ->assertSee('template-surat-peminjaman.docx');
});

test('panduan menampilkan 9 langkah tata cara dan catatan kalimat dalam kurung', function () {
    $this->get(route('panduan'))
        ->assertOk()
        ->assertSee('Pilih Fasilitas')
        ->assertSee('Pilih Tanggal Sewa')
        ->assertSee('Unduh Template Surat')
        ->assertSee('Siapkan dan Unggah Surat')
        ->assertSee('Ajukan Jadwal Sewa')
        ->assertSee('Menunggu Verifikasi')
        ->assertSee('Lakukan Pembayaran')
        ->assertSee('Unggah Bukti Pembayaran')
        ->assertSee('Penyewaan Berhasil')
        ->assertSee('tanda kurung', false);
});
