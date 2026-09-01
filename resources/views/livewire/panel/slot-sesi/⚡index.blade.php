<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Fasilitas;
use App\Models\SlotSesi;

new #[Title('Slot Sesi')] class extends Component
{
    use WithPagination;

    public int $fasilitasId = 0;

    public string $nama = '';
    public string $jamMulai = '08:00';
    public string $jamSelesai = '10:00';

    public ?int $editId = null;

    public function updatedFasilitasId(): void
    {
        $this->resetPage();
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset('nama', 'jamMulai', 'jamSelesai', 'editId');
    }

    public function simpan(): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $this->validate([
            'fasilitasId' => ['required', 'integer', 'exists:fasilitas,id'],
            'nama' => ['required', 'string', 'max:50'],
            'jamMulai' => ['required', 'date_format:H:i'],
            'jamSelesai' => ['required', 'date_format:H:i', 'after:jamMulai'],
        ]);

        SlotSesi::updateOrCreate(
            ['id' => $this->editId],
            [
                'fasilitas_id' => $this->fasilitasId,
                'nama' => $this->nama,
                'jam_mulai' => $this->jamMulai,
                'jam_selesai' => $this->jamSelesai,
            ],
        );

        session()->flash('status', $this->editId
            ? __('Slot sesi berhasil diperbarui.')
            : __('Slot sesi berhasil ditambahkan.'));

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $slot = SlotSesi::findOrFail($id);
        $this->editId = $slot->id;
        $this->fasilitasId = $slot->fasilitas_id;
        $this->nama = $slot->nama;
        $this->jamMulai = substr($slot->jam_mulai, 0, 5);
        $this->jamSelesai = substr($slot->jam_selesai, 0, 5);
    }

    public function hapus(int $id): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        SlotSesi::findOrFail($id)->delete();

        session()->flash('status', __('Slot sesi berhasil dihapus.'));
    }

    public function render()
    {
        return view('livewire::panel.slot-sesi.index', [
            'daftarFasilitas' => Fasilitas::orderBy('nama')->get(),
            'daftarSlot' => SlotSesi::query()
                ->when($this->fasilitasId > 0, fn ($q) => $q->where('fasilitas_id', $this->fasilitasId))
                ->with('fasilitas')
                ->orderBy('fasilitas_id')
                ->orderBy('jam_mulai')
                ->paginate(15),
        ])->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">{{ __('Slot Sesi') }}</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-4">
        @canany(['kelola_jadwal', 'kelola_fasilitas'])
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title h6">{{ $editId ? __('Ubah Slot') : __('Tambah Slot') }}</h5>
                        <form wire:submit="simpan" class="d-grid gap-3">
                            <div>
                                <label class="form-label" for="fasilitas">{{ __('Fasilitas') }}</label>
                                <select id="fasilitas" class="form-select" wire:model="fasilitasId" @if($editId) disabled @endif>
                                    <option value="0">{{ __('— Pilih fasilitas —') }}</option>
                                    @foreach ($daftarFasilitas as $f)
                                        <option value="{{ $f->id }}">{{ $f->nama }}</option>
                                    @endforeach
                                </select>
                                @error('fasilitasId')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label class="form-label" for="namaSlot">{{ __('Nama sesi') }}</label>
                                <input id="namaSlot" type="text" class="form-control" wire:model="nama" placeholder="Pagi" required>
                                @error('nama')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label" for="jamMulai">{{ __('Jam mulai') }}</label>
                                    <input id="jamMulai" type="time" class="form-control" wire:model="jamMulai" required>
                                    @error('jamMulai')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="jamSelesai">{{ __('Jam selesai') }}</label>
                                    <input id="jamSelesai" type="time" class="form-control" wire:model="jamSelesai" required>
                                    @error('jamSelesai')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">{{ __('Simpan') }}</button>
                                @if ($editId)
                                    <button type="button" class="btn btn-secondary btn-sm" wire:click="resetForm">{{ __('Batal') }}</button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcanany

        <div class="{{ auth()->user()?->hasAnyRole(['admin', 'pengelola']) ? 'col-lg-8' : 'col-12' }}">
            <div class="mb-3" style="max-width: 320px;">
                <select class="form-select" wire:model.live="fasilitasId">
                    <option value="0">{{ __('Semua fasilitas') }}</option>
                    @foreach ($daftarFasilitas as $f)
                        <option value="{{ $f->id }}">{{ $f->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Fasilitas') }}</th>
                            <th scope="col">{{ __('Sesi') }}</th>
                            <th scope="col">{{ __('Jam') }}</th>
                            <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarSlot as $slot)
                            <tr>
                                <td>{{ $slot->fasilitas->nama }}</td>
                                <td class="fw-semibold">{{ $slot->nama }}</td>
                                <td>{{ substr($slot->jam_mulai, 0, 5) }}–{{ substr($slot->jam_selesai, 0, 5) }}</td>
                                <td class="text-end">
                                    @canany(['kelola_jadwal', 'kelola_fasilitas'])
                                        <button wire:click="edit({{ $slot->id }})" class="btn btn-sm btn-outline-secondary">{{ __('Ubah') }}</button>
                                        <button wire:click="hapus({{ $slot->id }})"
                                                wire:confirm="{{ __('Hapus slot ini?') }}"
                                                class="btn btn-sm btn-outline-danger">{{ __('Hapus') }}</button>
                                    @endcanany
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">{{ __('Belum ada slot sesi.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $daftarSlot->links() }}
        </div>
    </div>
</div>