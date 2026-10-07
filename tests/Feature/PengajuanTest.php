<?php

use App\Mail\PengajuanDisetujuiMail;
use App\Mail\PengajuanDitolakMail;
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
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('local');
});

/**
 * Helper: pengajuan awal (menunggu_verifikasi) dengan surat resmi.
 */
function buatPengajuan(User $user, Fasilitas $fasilitas, SlotSesi $slot, string $tanggal): Peminjaman
{
    return app(BookingService::class)->create($user, [
        'fasilitas_id' => $fasilitas->id,
        'slot_sesi_id' => $slot->id,
        'tanggal' => $tanggal,
    ], UploadedFile::fake()->create('surat.pdf', 100));
}

test('create menyimpan surat resmi dan mencatat log pengajuan', function () {
    $user = User::factory()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = buatPengajuan($user, $fasilitas, $slot, today()->addDays(3)->toDateString());

    expect($peminjaman->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
    expect($peminjaman->expired_at)->toBeNull();

    $media = $peminjaman->getFirstMedia('surat_peminjaman');
    expect($media)->not->toBeNull();
    expect($media->disk)->toBe('local');

    expect(PeminjamanLog::where('peminjaman_id', $peminjaman->id)->count())->toBe(1);
    expect(PeminjamanLog::where('peminjaman_id', $peminjaman->id)->first()->ke_status)
        ->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
});

test('halaman form pengajuan menampilkan upload surat wajib dan menolak submit tanpa surat', function () {
    $user = User::factory()->pengguna()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $this->actingAs($user);

    // Regresi: variabel template `$slot` bentrok dengan slot-proxy Livewire —
    // halaman harus tetap ter-render dan menampilkan blok upload surat.
    Livewire::withQueryParams([
        'fasilitas' => $fasilitas->id,
        'slot' => $slot->id,
        'tanggal' => today()->addDays(3)->toDateString(),
    ])
        ->test('peminjaman.create')
        ->assertSee('Surat Peminjaman Resmi')
        ->call('submit')
        ->assertHasErrors(['surat']);

    // Submit tanpa surat tidak membuat peminjaman.
    expect(Peminjaman::count())->toBe(0);
});

test('pengguna dapat mengajukan peminjaman dengan surat lewat form', function () {
    Mail::fake();

    $user = User::factory()->pengguna()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $this->actingAs($user);

    Livewire::withQueryParams([
        'fasilitas' => $fasilitas->id,
        'slot' => $slot->id,
        'tanggal' => today()->addDays(3)->toDateString(),
    ])
        ->test('peminjaman.create')
        ->set('surat', UploadedFile::fake()->create('surat.pdf', 100))
        ->call('submit')
        ->assertHasNoErrors();

    $peminjaman = Peminjaman::first();
    expect($peminjaman)->not->toBeNull();
    expect($peminjaman->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
    expect($peminjaman->getFirstMedia('surat_peminjaman'))->not->toBeNull();
});

test('setujuiPengajuan memindahkan ke menunggu_pembayaran dan mengirim email', function () {
    Mail::fake();

    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = buatPengajuan($user, $fasilitas, $slot, today()->addDays(3)->toDateString());

    app(BookingService::class)->setujuiPengajuan($peminjaman, $pengelola);

    $peminjaman->refresh();
    expect($peminjaman->status)->toBe(Peminjaman::MENUNGGU_PEMBAYARAN);
    expect($peminjaman->expired_at)->not->toBeNull();
    expect($peminjaman->expired_at->greaterThan(now()->addHours(23)))->toBeTrue();

    Mail::assertQueued(PengajuanDisetujuiMail::class, function (PengajuanDisetujuiMail $mail) use ($user): bool {
        return $mail->hasTo($user->email);
    });

    expect((new PengajuanDisetujuiMail($peminjaman))->render())->toContain($peminjaman->kode);
});

test('tolakPengajuan tanpa catatan melempar ValidationException', function () {
    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();

    $peminjaman = buatPengajuan($user, $fasilitas, $slot, today()->addDays(3)->toDateString());

    expect(fn () => app(BookingService::class)->tolakPengajuan($peminjaman, '', $pengelola))
        ->toThrow(ValidationException::class);

    expect($peminjaman->fresh()->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
});

test('tolakPengajuan menyimpan catatan, mengirim email, dan slot tetap terkunci', function () {
    Mail::fake();

    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $tanggal = today()->addDays(3)->toDateString();

    $peminjaman = buatPengajuan($user, $fasilitas, $slot, $tanggal);

    app(BookingService::class)->tolakPengajuan($peminjaman, 'Surat belum bertanda tangan, mohon revisi', $pengelola);

    $peminjaman->refresh();
    expect($peminjaman->status)->toBe(Peminjaman::DITOLAK);
    expect($peminjaman->catatan_verifikasi)->toBe('Surat belum bertanda tangan, mohon revisi');
    expect($peminjaman->expired_at)->not->toBeNull();

    Mail::assertQueued(PengajuanDitolakMail::class, function (PengajuanDitolakMail $mail) use ($user): bool {
        return $mail->hasTo($user->email) && $mail->catatan === 'Surat belum bertanda tangan, mohon revisi';
    });

    expect((new PengajuanDitolakMail($peminjaman, 'Surat belum bertanda tangan, mohon revisi'))->render())
        ->toContain('Surat belum bertanda tangan, mohon revisi');

    // Slot tetap terkunci selama penyewa merevisi.
    expect(fn () => buatPengajuan(User::factory()->create(), $fasilitas, $slot, $tanggal))
        ->toThrow(ValidationException::class);
});

test('revisiPengajuan mengganti jadwal & surat, melepas slot lama, dan mengunci slot baru', function () {
    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slotLama = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $slotBaru = SlotSesi::factory()->for($fasilitas)->siang()->create();
    $tanggalLama = today()->addDays(3)->toDateString();
    $tanggalBaru = today()->addDays(4)->toDateString();

    $peminjaman = buatPengajuan($user, $fasilitas, $slotLama, $tanggalLama);
    app(BookingService::class)->tolakPengajuan($peminjaman, 'Mohon ganti jadwal', $pengelola);

    app(BookingService::class)->revisiPengajuan(
        $peminjaman->fresh(),
        ['slot_sesi_id' => $slotBaru->id, 'tanggal' => $tanggalBaru],
        UploadedFile::fake()->create('surat-baru.pdf', 120),
        $user,
    );

    $peminjaman->refresh();
    expect($peminjaman->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
    expect($peminjaman->catatan_verifikasi)->toBeNull();
    expect($peminjaman->expired_at)->toBeNull();
    expect($peminjaman->slot_sesi_id)->toBe($slotBaru->id);

    // Surat ter-replace (singleFile → tepat satu media).
    expect($peminjaman->getMedia('surat_peminjaman'))->toHaveCount(1);

    // Slot lama sudah lepas → user lain bisa booking.
    $slotLamaBaru = buatPengajuan(User::factory()->create(), $fasilitas, $slotLama, $tanggalLama);
    expect($slotLamaBaru->id)->not->toBe($peminjaman->id);

    // Slot baru terkunci.
    expect(fn () => buatPengajuan(User::factory()->create(), $fasilitas, $slotBaru, $tanggalBaru))
        ->toThrow(ValidationException::class);
});

test('revisiPengajuan ke slot yang sudah diambil peminjaman aktif lain ditolak', function () {
    $user = User::factory()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slot = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $slotLain = SlotSesi::factory()->for($fasilitas)->siang()->create();
    $tanggal = today()->addDays(3)->toDateString();

    $peminjaman = buatPengajuan($user, $fasilitas, $slot, $tanggal);
    app(BookingService::class)->tolakPengajuan($peminjaman, 'Mohon revisi', $pengelola);

    // Peminjaman aktif lain mengunci slot tujuan.
    buatPengajuan(User::factory()->create(), $fasilitas, $slotLain, $tanggal);

    try {
        app(BookingService::class)->revisiPengajuan(
            $peminjaman->fresh(),
            ['slot_sesi_id' => $slotLain->id, 'tanggal' => $tanggal],
            UploadedFile::fake()->create('surat-baru.pdf', 120),
            $user,
        );

        $this->fail('Revisi ke slot terisi seharusnya ditolak.');
    } catch (ValidationException $e) {
        expect($e->errors()['slot_sesi_id'][0])->toContain('Slot sudah dipesan');
    }
});

test('pengelola dapat menyetujui dan menolak pengajuan lewat halaman verifikasi', function () {
    Mail::fake();

    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slotA = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $slotB = SlotSesi::factory()->for($fasilitas)->siang()->create();

    $pengajuanA = buatPengajuan(User::factory()->create(), $fasilitas, $slotA, today()->addDays(3)->toDateString());
    $pengajuanB = buatPengajuan(User::factory()->create(), $fasilitas, $slotB, today()->addDays(4)->toDateString());

    $this->actingAs($pengelola);

    Livewire::test('verifikasi.index')
        ->assertSee($pengajuanA->kode)
        ->call('setujui', $pengajuanA->id);

    expect($pengajuanA->fresh()->status)->toBe(Peminjaman::MENUNGGU_PEMBAYARAN);

    // Tolak tanpa catatan → error validasi.
    Livewire::test('verifikasi.index')
        ->call('tolak', $pengajuanB->id)
        ->assertHasErrors(['catatan']);

    expect($pengajuanB->fresh()->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);

    // Tolak dengan catatan.
    Livewire::test('verifikasi.index')
        ->set('catatan', 'Surat tidak jelas')
        ->call('tolak', $pengajuanB->id)
        ->assertHasNoErrors();

    expect($pengajuanB->fresh()->status)->toBe(Peminjaman::DITOLAK);
    expect($pengajuanB->fresh()->catatan_verifikasi)->toBe('Surat tidak jelas');
});

test('pengguna dapat mengajukan ulang revisi lewat halaman revisi', function () {
    $user = User::factory()->pengguna()->create();
    $pengelola = User::factory()->pengelola()->create();
    $fasilitas = Fasilitas::factory()->create();
    $slotLama = SlotSesi::factory()->for($fasilitas)->pagi()->create();
    $slotBaru = SlotSesi::factory()->for($fasilitas)->siang()->create();

    $peminjaman = buatPengajuan($user, $fasilitas, $slotLama, today()->addDays(3)->toDateString());
    app(BookingService::class)->tolakPengajuan($peminjaman, 'Mohon revisi surat', $pengelola);

    $this->actingAs($user);

    Livewire::test('peminjaman.revisi', ['peminjaman' => $peminjaman->fresh()])
        ->assertSee('Mohon revisi surat')
        ->set('tanggal', today()->addDays(5)->toDateString())
        ->set('slotSesiId', $slotBaru->id)
        ->set('surat', UploadedFile::fake()->create('surat-baru.pdf', 120))
        ->call('submit')
        ->assertHasNoErrors();

    $peminjaman->refresh();
    expect($peminjaman->status)->toBe(Peminjaman::MENUNGGU_VERIFIKASI);
    expect($peminjaman->catatan_verifikasi)->toBeNull();
    expect($peminjaman->slot_sesi_id)->toBe($slotBaru->id);
});
