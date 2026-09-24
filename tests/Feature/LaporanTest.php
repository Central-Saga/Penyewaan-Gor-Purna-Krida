<?php

use App\Models\Fasilitas;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use App\Services\LaporanService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('laporan service mengagregasi data pemasukan sesuai transaksi terverifikasi', function () {
    Storage::fake('local');

    $pengguna = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $admin = User::factory()->admin()->create();

    $fasilitas = Fasilitas::factory()->create(['tarif_per_sesi' => 200000]);
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

    $service = app(LaporanService::class);
    $pemasukan = $service->pemasukan(today()->toDateString(), today()->toDateString());

    expect($pemasukan->count())->toBe(1);
    expect($pemasukan->first()->nominal)->toBe(200000);

    // Export CSV returns valid stream
    $this->actingAs($admin);
    $csvResponse = $this->get(route('laporan.export', [
        'jenis' => 'pemasukan',
        'format' => 'csv',
        'mulai' => today()->toDateString(),
        'sampai' => today()->toDateString(),
    ]));

    $csvResponse->assertOk();
    $csvResponse->assertHeader('content-type', 'text/csv; charset=UTF-8');
    $csvResponse->assertStreamed();
});

test('laporan peminjaman periode menghitung per fasilitas dan status', function () {
    $mulai = today()->startOfMonth()->toDateString();
    $sampai = today()->endOfMonth()->toDateString();

    $fasilitasA = Fasilitas::factory()->create(['nama' => 'Lapangan A']);
    $fasilitasB = Fasilitas::factory()->create(['nama' => 'Lapangan B']);
    $slotA = SlotSesi::factory()->for($fasilitasA)->pagi()->create();
    $slotB = SlotSesi::factory()->for($fasilitasB)->siang()->create();

    // 2 peminjaman di fasilitas A (status berbeda), 1 di fasilitas B.
    Peminjaman::factory()->for($fasilitasA)->for($slotA, 'slotSesi')->create([
        'tanggal' => today()->toDateString(),
        'status' => Peminjaman::MENUNGGU_PEMBAYARAN,
    ]);
    Peminjaman::factory()->for($fasilitasA)->for($slotA, 'slotSesi')->create([
        'tanggal' => today()->toDateString(),
        'status' => Peminjaman::DISETUJUI,
    ]);
    Peminjaman::factory()->for($fasilitasB)->for($slotB, 'slotSesi')->create([
        'tanggal' => today()->toDateString(),
        'status' => Peminjaman::DISETUJUI,
    ]);

    // 1 peminjaman di luar periode — tidak boleh ikut terhitung.
    Peminjaman::factory()->for($fasilitasA)->for($slotA, 'slotSesi')->create([
        'tanggal' => today()->addMonth()->toDateString(),
        'status' => Peminjaman::DISETUJUI,
    ]);

    $data = app(LaporanService::class)->peminjaman($mulai, $sampai);

    expect($data['daftar'])->toHaveCount(3);
    expect($data['perFasilitas']['Lapangan A'])->toBe(2);
    expect($data['perFasilitas']['Lapangan B'])->toBe(1);
    expect($data['perStatus'][Peminjaman::MENUNGGU_PEMBAYARAN])->toBe(1);
    expect($data['perStatus'][Peminjaman::DISETUJUI])->toBe(2);
    expect($data['perStatus'][Peminjaman::SELESAI])->toBe(0);
});

test('export csv laporan peminjaman berisi baris data', function () {
    $admin = User::factory()->admin()->create();
    $fasilitas = Fasilitas::factory()->create(['nama' => 'Lapangan Utama']);
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create(['nama' => 'Pagi']);
    $peminjaman = Peminjaman::factory()->for($fasilitas)->for($slot, 'slotSesi')->create([
        'tanggal' => today()->toDateString(),
        'status' => Peminjaman::MENUNGGU_PEMBAYARAN,
    ]);

    $this->actingAs($admin);

    $response = $this->get(route('laporan.export', [
        'jenis' => 'peminjaman',
        'format' => 'csv',
        'mulai' => today()->startOfMonth()->toDateString(),
        'sampai' => today()->endOfMonth()->toDateString(),
    ]));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    $response->assertStreamed();

    $csv = $response->streamedContent();

    // Regresi bug `nama_sesi`: kolom Sesi harus berisi nama sesi, bukan error 500.
    expect($csv)->toContain($peminjaman->kode);
    expect($csv)->toContain('Lapangan Utama');
    expect($csv)->toContain('Pagi');
});

test('export pdf laporan pemasukan menghasilkan pdf', function () {
    Storage::fake('local');

    $admin = User::factory()->admin()->create();
    $pengguna = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create(['tarif_per_sesi' => 100000]);
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = app(BookingService::class)->create($pengguna, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ]);
    $file = UploadedFile::fake()->image('bukti.jpg');
    $pembayaran = app(PaymentService::class)->upload($peminjaman, $file, 'transfer', $pengguna);
    app(PaymentService::class)->verifikasi($pembayaran, true, null, $pengelola);

    $this->actingAs($admin);

    $response = $this->get(route('laporan.export', [
        'jenis' => 'pemasukan',
        'format' => 'pdf',
        'mulai' => today()->toDateString(),
        'sampai' => today()->toDateString(),
    ]));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});
