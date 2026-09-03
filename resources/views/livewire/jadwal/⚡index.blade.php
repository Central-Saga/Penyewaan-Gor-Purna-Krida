<?php

use Livewire\Attributes\Computed;
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
    #[Computed]
    public function slotGrid(): array
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
            ->whereDate('tanggal', $this->tanggal)
            ->whereIn('status', Peminjaman::STATUS_AKTIF)
            ->pluck('slot_sesi_id')
            ->all();

        $diblokir = BlokirSlot::query()
            ->where('fasilitas_id', $this->fasilitasId)
            ->whereDate('tanggal', $this->tanggal)
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ __('Jadwal & Ketersediaan Slot') }}</h1>
            <p class="text-secondary mb-0">{{ __('Pilih fasilitas dan tanggal untuk melihat slot sesi yang tersedia.') }}</p>
        </div>
    </div>

    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-body p-4">
            <form wire:submit.prevent class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small text-secondary fw-semibold" for="fasilitas">
                        <i class="bi bi-grid-3x3-gap me-1 text-primary"></i> {{ __('Fasilitas Gelanggang') }}
                    </label>
                    <select id="fasilitas" class="form-select" wire:model.live="fasilitasId">
                        @foreach ($daftarFasilitas as $f)
                            <option value="{{ $f->id }}">{{ $f->nama }} (Rp {{ number_format($f->tarif_per_sesi, 0, ',', '.') }}/sesi)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-secondary fw-semibold" for="tanggal">
                        <i class="bi bi-calendar-date me-1 text-primary"></i> {{ __('Pilih Tanggal Sewa') }}
                    </label>
                    <input id="tanggal" type="date" class="form-control" wire:model.live="tanggal"
                           min="{{ today()->toDateString() }}">
                </div>
            </form>
        </div>
    </div>

    @if ($fasilitasId === 0)
        <div class="alert alert-light border rounded-4 text-center py-4">{{ __('Pilih fasilitas untuk melihat jadwal.') }}</div>
    @else
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-transparent border-0 p-4 pb-2">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <h5 class="fw-bold text-dark mb-0">
                        {{ __('Daftar Sesi (:tgl)', ['tgl' => \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y')]) }}
                    </h5>
                    <div class="d-flex flex-wrap gap-2 small">
                        <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Tersedia (Bisa Disewa)
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-2.5 py-1">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Terisi / Dibooking
                        </span>
                        <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> Diblokir / Perawatan
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="row g-3" wire:key="grid-{{ $fasilitasId }}-{{ $tanggal }}">
                    @forelse ($this->slotGrid as $slot)
                        <div class="col-sm-6 col-lg-4">
                            @if ($slot['status'] === 'tersedia')
                                <button wire:click="pilihSlot({{ $slot['id'] }})"
                                        class="btn btn-outline-primary border-2 rounded-4 w-100 p-3 text-start shadow-sm d-flex flex-column justify-content-between h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-2 w-100">
                                        <span class="badge bg-primary text-white rounded-pill px-2.5">{{ __('Tersedia') }}</span>
                                        <i class="bi bi-arrow-right-circle fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-6 text-dark">{{ $slot['label'] }}</div>
                                        <div class="small text-primary fw-medium">{{ __('Klik untuk pesan slot ini') }}</div>
                                    </div>
                                </button>
                            @elseif ($slot['status'] === 'terisi')
                                <div class="p-3 border rounded-4 bg-light text-secondary d-flex flex-column justify-content-between h-100 opacity-75">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-secondary text-white rounded-pill px-2.5">{{ __('Sudah Dipesan') }}</span>
                                        <i class="bi bi-lock-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-6 text-dark">{{ $slot['label'] }}</div>
                                        <div class="small text-muted">{{ __('Slot sedang terisi') }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 border border-danger border-opacity-25 rounded-4 bg-danger-subtle text-danger d-flex flex-column justify-content-between h-100 opacity-75">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-danger text-white rounded-pill px-2.5">{{ __('Diblokir') }}</span>
                                        <i class="bi bi-slash-circle-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-6 text-danger-emphasis">{{ $slot['label'] }}</div>
                                        <div class="small text-danger">{{ __('Tidak dapat disewa') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light border rounded-4 text-center py-4 text-secondary mb-0">
                                <i class="bi bi-calendar-x fs-2 text-muted d-block mb-1"></i>
                                {{ __('Belum ada slot sesi yang dikonfigurasi untuk fasilitas ini.') }}
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>
