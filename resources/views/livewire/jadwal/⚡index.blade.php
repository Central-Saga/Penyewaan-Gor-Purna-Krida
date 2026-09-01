<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Fasilitas;
use App\Models\SlotSesi;
use App\Models\Peminjaman;
use App\Models\BlokirSlot;

new #[Title('Jadwal')] class extends Component
{
    public int $fasilitasId = 0;

    public string $tanggal = '';

    public function mount(): void
    {
        $this->fasilitasId = (int) (Fasilitas::aktif()->orderBy('nama')->first()?->id ?? 0);
        $this->tanggal = today()->toDateString();
    }

    /**
     * Status tiap slot untuk tanggal terpilih:
     * tersedia / terisi (booking aktif) / diblokir.
     *
     * @return array<int, array{id: int, label: string, status: string}>
     */
    public function getSlotGridProperty(): array
    {
        if ($this->fasilitasId === 0 || $this->tanggal === '') {
            return [];
        }

        $slots = SlotSesi::query()
            ->where('fasilitas_id', $this->fasilitasId)
            ->orderBy('jam_mulai')
            ->get();

        $terisi = Peminjaman::query()
            ->where('fasilitas_id', $this->fasilitasId)
            ->where('tanggal', $this->tanggal)
            ->whereIn('status', Peminjaman::STATUS_AKTIF)
            ->pluck('slot_sesi_id')
            ->all();

        $diblokir = BlokirSlot::query()
            ->where('fasilitas_id', $this->fasilitasId)
            ->where('tanggal', $this->tanggal)
            ->pluck('slot_sesi_id')
            ->all();

        return $slots->map(fn (SlotSesi $slot) => [
            'id' => $slot->id,
            'label' => $slot->label,
            'status' => in_array($slot->id, $diblokir, true) ? 'diblokir'
                : (in_array($slot->id, $terisi, true) ? 'terisi' : 'tersedia'),
        ])->all();
    }

    public function pilihSlot(int $slotSesiId): void
    {
        $grid = collect($this->slotGrid)->firstWhere('id', $slotSesiId);

        if ($grid === null || $grid['status'] !== 'tersedia') {
            return;
        }

        $this->redirectRoute('peminjaman.create', [
            'fasilitas' => $this->fasilitasId,
            'slot' => $slotSesiId,
            'tanggal' => $this->tanggal,
        ], navigate: true);
    }

    public function render()
    {
        return $this->view(['daftarFasilitas' => Fasilitas::aktif()->orderBy('nama')->get()])
            ->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">{{ __('Jadwal Fasilitas') }}</h1>

    <form wire:submit.prevent class="row g-2 mb-4">
        <div class="col-md-5">
            <label class="form-label" for="fasilitas">{{ __('Fasilitas') }}</label>
            <select id="fasilitas" class="form-select" wire:model.live="fasilitasId">
                @foreach ($daftarFasilitas as $f)
                    <option value="{{ $f->id }}">{{ $f->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="tanggal">{{ __('Tanggal') }}</label>
            <input id="tanggal" type="date" class="form-control" wire:model.live="tanggal"
                   min="{{ today()->toDateString() }}" value="{{ $tanggal }}">
        </div>
    </form>

    @if ($fasilitasId === 0)
        <div class="alert alert-light border">{{ __('Pilih fasilitas untuk melihat jadwal.') }}</div>
    @else
        <div class="row g-3" wire:key="grid-{{ $fasilitasId }}-{{ $tanggal }}">
            @forelse ($slotGrid as $slot)
                <div class="col-sm-6 col-lg-4">
                    @if ($slot['status'] === 'tersedia')
                        <button wire:click="pilihSlot({{ $slot['id'] }})"
                                class="btn btn-primary w-100 py-3 text-start">
                            <div class="fw-semibold">{{ $slot['label'] }}</div>
                            <div class="small">{{ __('Klik untuk sewa') }}</div>
                        </button>
                    @elseif ($slot['status'] === 'terisi')
                        <div class="btn btn-secondary disabled w-100 py-3 text-start" aria-disabled="true">
                            <div class="fw-semibold">{{ $slot['label'] }}</div>
                            <div class="small">{{ __('Terisi') }}</div>
                        </div>
                    @else
                        <div class="btn btn-danger disabled w-100 py-3 text-start" aria-disabled="true">
                            <div class="fw-semibold">{{ $slot['label'] }}</div>
                            <div class="small">{{ __('Diblokir') }}</div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border">{{ __('Tidak ada slot sesi untuk fasilitas ini.') }}</div>
                </div>
            @endforelse
        </div>
    @endif
</div>