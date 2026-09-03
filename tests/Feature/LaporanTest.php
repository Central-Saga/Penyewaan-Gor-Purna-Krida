<?php

use App\Models\Fasilitas;
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
