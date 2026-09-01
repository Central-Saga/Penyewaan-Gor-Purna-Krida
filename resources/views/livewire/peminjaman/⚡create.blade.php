<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Fasilitas;
use App\Models\SlotSesi;
use App\Services\BookingService;

new #[Title('Form Peminjaman')] class extends Component
{
    public Fasilitas $fasilitas;

    public SlotSesi $slot;

    public string $tanggal = '';

    public function mount(): void
    {
        $this->tanggal = request()->query('tanggal', today()->toDateString());
    }

    public function submit(BookingService $bookingService)
    {
        $user = auth()->user();

        if ($user === null || ! $user->hasRole('pengguna')) {
            abort(403, __('Hanya pengguna yang dapat membuat peminjaman.'));
        }

        $peminjaman = $bookingService->create($user, [
            'fasilitas_id' => $this->fasilitas->id,
            'slot_sesi_id' => $this->slot->id,
            'tanggal' => $this->tanggal,
        ]);

        session()->flash('status', __('Peminjaman :kode dibuat. Selesaikan pembayaran.', [
            'kode' => $peminjaman->kode,
        ]));

        return $this->redirectRoute('peminjaman.index', navigate: true);
    }

    public function render()
    {
        return $this->view()
            ->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">{{ __('Form Peminjaman') }}</h1>

    <div class="card" style="max-width: 640px;">
        <div class="card-body">
            <h5 class="card-title">{{ $fasilitas->nama }}</h5>
            <p class="card-subtitle text-secondary mb-3">
                {{ ucfirst($fasilitas->jenis) }} — kapasitas {{ $fasilitas->kapasitas }} orang
            </p>

            <dl class="row mb-4">
                <dt class="col-5">{{ __('Tanggal') }}</dt>
                <dd class="col-7">{{ \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</dd>
                <dt class="col-5">{{ __('Slot') }}</dt>
                <dd class="col-7">{{ $slot->label }}</dd>
                <dt class="col-5">{{ __('Tarif') }}</dt>
                <dd class="col-7 fw-bold">Rp {{ number_format($fasilitas->tarif_per_sesi, 0, ',', '.') }}</dd>
            </dl>

            <form wire:submit="submit" class="d-grid gap-3">
                <button type="submit" class="btn btn-primary">{{ __('Kirim Peminjaman') }}</button>
                <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">{{ __('Kembali ke Jadwal') }}</a>
            </form>
        </div>
    </div>
</div>