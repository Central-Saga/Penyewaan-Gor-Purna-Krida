<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Fasilitas;

new #[Title('Fasilitas')] class extends Component
{
    use WithPagination;

    public string $cari = '';
    public string $jenis = '';
    public string $status = '';

    public function updatingCari(): void
    {
        $this->resetPage();
    }

    public function nonaktifkan(int $id): void
    {
        $this->authorizeKemampuan();

        Fasilitas::findOrFail($id)->update(['status_aktif' => false]);
    }

    public function aktifkan(int $id): void
    {
        $this->authorizeKemampuan();

        Fasilitas::findOrFail($id)->update(['status_aktif' => true]);
    }

    public function hapus(int $id): void
    {
        $this->authorizeKemampuan();

        Fasilitas::findOrFail($id)->delete();
    }

    private function authorizeKemampuan(): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }
    }

    public function render()
    {
        $fasilitas = Fasilitas::query()
            ->when($this->cari, fn ($q) => $q->where('nama', 'like', "%{$this->cari}%"))
            ->when($this->jenis !== '', fn ($q) => $q->where('jenis', $this->jenis))
            ->when($this->status !== '', fn ($q) => $q->where('status_aktif', $this->status === 'aktif'))
            ->orderBy('nama')
            ->paginate(10);

        return $this->view(['daftarFasilitas' => $fasilitas])
            ->layout('layouts.app');
    }
}; ?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">{{ __('Fasilitas') }}</h1>

        @canany(['kelola_fasilitas'])
            <a href="{{ route('panel.fasilitas.create') }}" class="btn btn-primary">
                {{ __('Tambah Fasilitas') }}
            </a>
        @endcanany
    </div>

    <form wire:submit.prevent="$refresh" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" placeholder="{{ __('Cari nama fasilitas...') }}"
                   wire:model.live.debounce.300ms="cari">
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="jenis">
                <option value="">{{ __('Semua jenis') }}</option>
                <option value="indoor">{{ __('Indoor') }}</option>
                <option value="outdoor">{{ __('Outdoor') }}</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="status">
                <option value="">{{ __('Semua status') }}</option>
                <option value="aktif">{{ __('Aktif') }}</option>
                <option value="nonaktif">{{ __('Nonaktif') }}</option>
            </select>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">{{ __('Nama') }}</th>
                    <th scope="col">{{ __('Jenis') }}</th>
                    <th scope="col">{{ __('Kapasitas') }}</th>
                    <th scope="col">{{ __('Tarif/Sesi') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarFasilitas as $fasilitas)
                    <tr>
                        <td class="fw-semibold">{{ $fasilitas->nama }}</td>
                        <td>{{ ucfirst($fasilitas->jenis) }}</td>
                        <td>{{ $fasilitas->kapasitas }}</td>
                        <td>Rp {{ number_format($fasilitas->tarif_per_sesi, 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$fasilitas->status_aktif ? 'aktif' : 'nonaktif'" /></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-2">
                                <a href="{{ route('panel.fasilitas.edit', $fasilitas) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   @cannot('kelola_fasilitas') disabled @endcannot>{{ __('Ubah') }}</a>

                                @can('kelola_fasilitas')
                                    @if ($fasilitas->status_aktif)
                                        <button wire:click="nonaktifkan({{ $fasilitas->id }})"
                                                wire:confirm="{{ __('Nonaktifkan fasilitas ini?') }}"
                                                class="btn btn-sm btn-outline-warning">{{ __('Nonaktifkan') }}</button>
                                    @else
                                        <button wire:click="aktifkan({{ $fasilitas->id }})"
                                                class="btn btn-sm btn-outline-success">{{ __('Aktifkan') }}</button>
                                    @endif

                                    <button wire:click="hapus({{ $fasilitas->id }})"
                                            wire:confirm="{{ __('Hapus fasilitas ini? Data tidak dapat dikembalikan.') }}"
                                            class="btn btn-sm btn-outline-danger">{{ __('Hapus') }}</button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            {{ __('Belum ada fasilitas.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $daftarFasilitas->links() }}
</div>