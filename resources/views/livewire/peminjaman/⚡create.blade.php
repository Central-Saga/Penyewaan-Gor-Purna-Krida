<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Fasilitas;
use App\Models\SlotSesi;
use App\Services\BookingService;

new #[Title('Form Peminjaman')] class extends Component
{
    public ?Fasilitas $fasilitas = null;

    public ?SlotSesi $slot = null;

    public string $tanggal = '';

    public function mount(): void
    {
        $fasilitasId = (int) request()->query('fasilitas', 0);
        $slotId = (int) request()->query('slot', 0);
        $this->tanggal = request()->query('tanggal', today()->toDateString());

        $this->fasilitas = Fasilitas::find($fasilitasId);
        $this->slot = SlotSesi::find($slotId);

        if (! $this->fasilitas || ! $this->slot) {
            $this->redirectRoute('jadwal.index', navigate: true);
        }
    }

    public function submit(BookingService $bookingService)
    {
        $user = auth()->user();

        if ($user === null || ! $user->hasRole('pengguna')) {
            abort(403, __('Hanya pengguna yang dapat membuat peminjaman.'));
        }

        if (! $this->fasilitas || ! $this->slot) {
            return;
        }

        $peminjaman = $bookingService->create($user, [
            'fasilitas_id' => $this->fasilitas->id,
            'slot_sesi_id' => $this->slot->id,
            'tanggal' => $this->tanggal,
        ]);

        session()->flash('status', __('Peminjaman :kode berhasil dibuat. Silakan selesaikan pembayaran.', [
            'kode' => $peminjaman->kode,
        ]));

        return $this->redirectRoute('pembayaran.show', $peminjaman, navigate: true);
    }

    public function render()
    {
        return $this->view()
            ->layout('layouts.app');
    }
}; ?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ __('Konfirmasi Pemesanan Gelanggang') }}</h1>
            <p class="text-secondary mb-0">{{ __('Periksa kembali detail jadwal sewa sebelum mengirimkan pengajuan.') }}</p>
        </div>
    </div>

    @if ($fasilitas && $slot)
        <div class="row g-4 justify-content-center">
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4">
                    <div class="card-header p-4 text-white border-0" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 small">
                                {{ ucfirst($fasilitas->jenis) }}
                            </span>
                            <span class="text-light text-opacity-75 small">
                                <i class="bi bi-people-fill me-1"></i> Kapasitas {{ $fasilitas->kapasitas }} orang
                            </span>
                        </div>
                        <h3 class="h4 fw-bold text-white mb-0">{{ $fasilitas->nama }}</h3>
                    </div>

                    <div class="card-body p-4">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-3">{{ __('Rincian Pemesanan') }}</h6>

                        <div class="list-group list-group-flush mb-4">
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2 text-secondary">
                                    <i class="bi bi-calendar-event fs-5 text-primary"></i>
                                    <span>{{ __('Tanggal Sewa') }}</span>
                                </div>
                                <div class="fw-bold text-dark text-end">
                                    {{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                                </div>
                            </div>

                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2 text-secondary">
                                    <i class="bi bi-clock-history fs-5 text-primary"></i>
                                    <span>{{ __('Sesi & Waktu') }}</span>
                                </div>
                                <div class="fw-bold text-dark text-end">
                                    {{ $slot->nama_sesi }}
                                    <div class="small text-secondary fw-normal">
                                        ({{ substr($slot->jam_mulai, 0, 5) }} - {{ substr($slot->jam_selesai, 0, 5) }} WITA)
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2 text-secondary">
                                    <i class="bi bi-person fs-5 text-primary"></i>
                                    <span>{{ __('Penyewa') }}</span>
                                </div>
                                <div class="fw-bold text-dark text-end">
                                    {{ auth()->user()->name }}
                                    <div class="small text-secondary fw-normal">
                                        {{ auth()->user()->no_hp ?? auth()->user()->email }}
                                    </div>
                                </div>
                            </div>

                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-light rounded-3 p-3 mt-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-cash-stack fs-4 text-success"></i>
                                    <span class="fw-bold text-dark">{{ __('Total Biaya Sewa') }}</span>
                                </div>
                                <div class="h4 fw-bold text-success mb-0">
                                    Rp {{ number_format($fasilitas->tarif_per_sesi, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 rounded-3 small d-flex gap-2 mb-4">
                            <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                            <div>
                                {{ __('Setelah pengajuan dibuat, Anda memiliki waktu 24 jam untuk menyelesaikan pembayaran dan mengunggah bukti transfer sebelum slot dilepas secara otomatis.') }}
                            </div>
                        </div>

                        <form wire:submit="submit" class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 flex-grow-1 fw-semibold shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> {{ __('Ajukan Peminjaman & Bayar') }}
                            </button>
                            <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4" wire:navigate>
                                {{ __('Batal') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
