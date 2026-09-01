<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Fasilitas;

new #[Title('Form Fasilitas')] class extends Component
{
    use WithFileUploads;

    public ?Fasilitas $fasilitas = null;

    public string $nama = '';
    public string $jenis = 'indoor';
    public string $deskripsi = '';
    public int|string $kapasitas = 20;
    public int|string $tarif = 50000;
    public bool $statusAktif = true;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $foto;

    public function mount(?Fasilitas $fasilitas = null): void
    {
        if ($fasilitas->exists) {
            $this->fasilitas = $fasilitas;
            $this->nama = $fasilitas->nama;
            $this->jenis = $fasilitas->jenis;
            $this->deskripsi = $fasilitas->deskripsi ?? '';
            $this->kapasitas = $fasilitas->kapasitas;
            $this->tarif = $fasilitas->tarif_per_sesi;
            $this->statusAktif = $fasilitas->status_aktif;
        }
    }

    public function simpan(): void
    {
        $this->validate([
            'nama' => ['required', 'string', 'max:150'],
            'jenis' => ['required', 'in:indoor,outdoor'],
            'deskripsi' => ['nullable', 'string'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'tarif' => ['required', 'integer', 'min:0'],
            'statusAktif' => ['boolean'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $data = [
            'nama' => $this->nama,
            'jenis' => $this->jenis,
            'deskripsi' => $this->deskripsi ?: null,
            'kapasitas' => $this->kapasitas,
            'tarif_per_sesi' => $this->tarif,
            'status_aktif' => $this->statusAktif,
        ];

        if ($this->fasilitas?->exists) {
            $this->fasilitas->update($data);
            $fasilitas = $this->fasilitas;
            $pesan = __('Fasilitas berhasil diperbarui.');
        } else {
            $fasilitas = Fasilitas::create($data);
            $pesan = __('Fasilitas berhasil ditambahkan.');
        }

        if ($this->foto) {
            $fasilitas->clearMediaCollection('foto');
            $fasilitas->addMedia($this->foto->getRealPath())
                ->usingName($this->nama)
                ->usingFileName($this->foto->hashName())
                ->toMediaCollection('foto', 'public');
        }

        session()->flash('status', $pesan);

        $this->redirectRoute('panel.fasilitas.index', navigate: true);
    }

    public function render()
    {
        return $this->view()
            ->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">
        {{ $fasilitas?->exists ? __('Ubah Fasilitas') : __('Tambah Fasilitas') }}
    </h1>

    <form wire:submit="simpan" class="row g-3" style="max-width: 720px;">
        <div class="col-md-6">
            <label for="nama" class="form-label">{{ __('Nama') }}</label>
            <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror"
                   wire:model="nama" required>
            @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="jenis" class="form-label">{{ __('Jenis') }}</label>
            <select id="jenis" class="form-select" wire:model="jenis">
                <option value="indoor">{{ __('Indoor') }}</option>
                <option value="outdoor">{{ __('Outdoor') }}</option>
            </select>
        </div>

        <div class="col-12">
            <label for="deskripsi" class="form-label">{{ __('Deskripsi') }}</label>
            <textarea id="deskripsi" class="form-control" rows="3" wire:model="deskripsi"></textarea>
 @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="kapasitas" class="form-label">{{ __('Kapasitas (orang)') }}</label>
            <input id="kapasitas" type="number" min="1" class="form-control @error('kapasitas') is-invalid @enderror"
                   wire:model="kapasitas" required>
            @error('kapasitas')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label for="tarif" class="form-label">{{ __('Tarif per Sesi (Rp)') }}</label>
            <input id="tarif" type="number" min="0" step="1000" class="form-control @error('tarif') is-invalid @enderror"
                   wire:model="tarif" required>
            @error('tarif')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12">
            <label for="foto" class="form-label">{{ __('Foto (JPG/PNG, maks 2MB)') }}</label>
            <input id="foto" type="file" accept="image/jpeg,image/png"
                   class="form-control @error('foto') is-invalid @enderror" wire:model="foto">
            @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 form-check form-switch">
            <input id="statusAktif" type="checkbox" class="form-check-input" role="switch" wire:model="statusAktif">
            <label class="form-check-label" for="statusAktif">{{ __('Fasilitas aktif (tampil di daftar publik)') }}</label>
        </div>

        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                {{ __('Simpan') }}
            </button>
            <a href="{{ route('panel.fasilitas.index') }}" class="btn btn-secondary">{{ __('Batal') }}</a>
        </div>
    </form>
</div>