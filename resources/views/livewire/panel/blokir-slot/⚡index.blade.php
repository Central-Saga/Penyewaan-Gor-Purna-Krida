<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BlokirSlot;
use App\Models\Fasilitas;
use App\Models\SlotSesi;

new #[Title('Blokir Slot')] class extends Component
{
    use WithPagination;

    public int $fasilitasId = 0;
    public int $slotSesiId = 0;
    public string $tanggal = '';
    public string $alasan = '';

    public function tambah(): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $this->validate([
            'fasilitasId' => ['required', 'integer', 'min:1'],
            'slotSesiId' => ['required', 'integer', 'min:1'],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'alasan' => ['required', 'string', 'max:255'],
        ]);

        BlokirSlot::updateOrCreate(
            [
                'fasilitas_id' => $this->fasilitasId,
                'slot_sesi_id' => $this->slotSesiId,
                'tanggal' => $this->tanggal,
            ],
            [
                'alasan' => $this->alasan,
                'diblokir_oleh' => auth()->id(),
            ],
        );

        session()->flash('status', __('Slot berhasil diblokir.'));
        $this->reset('slotSesiId', 'tanggal', 'alasan');
    }

    public function hapus(int $id): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        BlokirSlot::findOrFail($id)->delete();

        session()->flash('status', __('Blokir dihapus.'));
    }

    public function render()
    {
        return view('livewire::panel.blokir-slot.index', [
            'daftarFasilitas' => Fasilitas::orderBy('nama')->get(),
            'daftarSlot' => $this->fasilitasId > 0
                ? SlotSesi::where('fasilitas_id', $this->fasilitasId)->orderBy('jam_mulai')->get()
                : collect(),
            'daftarBlokir' => BlokirSlot::query()
                ->when($this->fasilitasId > 0, fn ($q) => $q->where('fasilitas_id', $this->fasilitasId))
                ->with(['fasilitas', 'slotSesi', 'diblokir'])
                ->orderByDesc('tanggal')
                ->paginate(15),
        ])->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">{{ __('Blokir Slot') }}</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-4">
        @canany(['kelola_jadwal', 'kelola_fasilitas'])
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title h6">{{ __('Tambah Blokir') }}</h5>
                        <form wire:submit="tambah" class="d-grid gap-3">
                            <div>
                                <label class="form-label" for="fasilitas">{{ __('Fasilitas') }}</label>
                                <select id="fasilitas" class="form-select" wire:model.live="fasilitasId">
                                    <option value="">{{ __('— Pilih fasilitas —') }}</option>
                                    @foreach ($daftarFasilitas as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama }}</option>
                                    @endforeach
                                </select>
                                @error('fasilitasId')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="slot">{{ __('Slot sesi') }}</label>
                                <select id="slot" class="form-select" wire:model="slotSesiId" @disabled($fasilitasId === 0)>
                                    <option value="">{{ __('— Pilih slot —') }}</option>
                                    @foreach ($daftarSlot as $slot)
                                        <option value="{{ $slot->id }}">{{ $slot->label }}</option>
                                    @endforeach
                                </select>
                                @error('slotSesiId')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="tanggal">{{ __('Tanggal') }}</label>
                                <input id="tanggal" type="date" class="form-control" wire:model="tanggal" required>
                                @error('tanggal')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="alasan">{{ __('Alasan') }}</label>
                                <input id="alasan" type="text" class="form-control" wire:model="alasan"
                                       placeholder="{{ __('mis. Perawatan lapangan') }}" required>
                                @error('alasan')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Blokir') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @endcanany

        <div class="{{ auth()->user()?->hasAnyRole(['admin', 'pengelola']) ? 'col-lg-8' : 'col-12' }}">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Tanggal') }}</th>
                            <th scope="col">{{ __('Fasilitas') }}</th>
                            <th scope="col">{{ __('Slot') }}</th>
                            <th scope="col">{{ __('Alasan') }}</th>
                            <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarBlokir as $blokir)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($blokir->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ $blokir->fasilitas->nama }}</td>
                                <td>{{ $blokir->slotSesi->nama }}</td>
                                <td class="small">{{ $blokir->alasan }}</td>
                                <td class="text-end">
                                    @canany(['kelola_jadwal', 'kelola_fasilitas'])
                                        <button wire:click="hapus({{ $blokir->id }})"
                                                wire:confirm="{{ __('Hapus blokir ini?') }}"
                                                class="btn btn-sm btn-outline-danger">{{ __('Hapus') }}</button>
                                    @endcanany
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">{{ __('Belum ada blokir slot.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $daftarBlokir->links() }}
        </div>
    </div>
</div>