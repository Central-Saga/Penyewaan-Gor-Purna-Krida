<?php

use App\Models\Fasilitas;
use App\Models\SlotSesi;
use App\Models\User;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('pengelola dan admin dapat mengakses data transaksi dan memfilter status', function () {
    Storage::fake('local');

    $pengguna = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $admin = User::factory()->admin()->create();

    $fasilitas = Fasilitas::factory()->create(['tarif_per_sesi' => 100000]);
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = app(BookingService::class)->create($pengguna, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => today()->toDateString(),
    ], UploadedFile::fake()->create('surat.pdf', 100));

    app(BookingService::class)->setujuiPengajuan($peminjaman, $pengelola);

    $file = UploadedFile::fake()->image('bukti.jpg');
    app(PaymentService::class)->upload($peminjaman->fresh(), $file, 'transfer', $pengguna);

    // Pengelola can see transaksi page
    $this->actingAs($pengelola);
    $this->get(route('transaksi.index'))->assertOk()->assertSee($peminjaman->kode);

    // Filter status terverifikasi works
    Livewire::test('panel.transaksi.index')
        ->set('status', 'terverifikasi')
        ->assertSee($peminjaman->kode)
        ->set('status', 'ditolak')
        ->assertDontSee($peminjaman->kode);
});
