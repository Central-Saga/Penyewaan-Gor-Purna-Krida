<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\BlokirSlot;
use App\Models\Peminjaman;
use App\Models\SlotSesi;
use App\Services\BookingService;

new #[Title('Revisi Pengajuan')] class extends Component
{
    use WithFileUploads;

    public Peminjaman $peminjaman;

    public string $tanggal = '';

    public ?int $slotSesiId = null;

    public $surat;

    public function mount(Peminjaman $peminjaman): void
    {
        $user = auth()->user();

        if ($peminjaman->user_id !== $user?->id || $peminjaman->status !== Peminjaman::DITOLAK) {
            $this->redirectRoute('peminjaman.index', navigate: true);

            return;
        }

        $this->peminjaman = $peminjaman;
        $this->tanggal = $peminjaman->tanggal->toDateString();
        $this->slotSesiId = $peminjaman->slot_sesi_id;
    }

    /**
     * Status tiap slot fasilitas pada tanggal terpilih.
     * Kecualikan record sendiri agar jadwal yang sama tetap dapat dipertahankan.
     *
     * @return array<int, array{id: int, label: string, status: string}>
     */
    #[Computed]
    public function slotTersedia(): array
    {
        if ($this->tanggal === '') {
            return [];
        }

        $slots = SlotSesi::query()
            ->where('fasilitas_id', $this->peminjaman->fasilitas_id)
            ->orderBy('jam_mulai')
            ->get();

        $terisi = Peminjaman::query()
            ->where('fasilitas_id', $this->peminjaman->fasilitas_id)
            ->whereDate('tanggal', $this->tanggal)
            ->where('id', '!=', $this->peminjaman->id)
            ->whereIn('status', Peminjaman::STATUS_AKTIF)
            ->pluck('slot_sesi_id')
            ->all();

        $diblokir = BlokirSlot::query()
            ->where('fasilitas_id', $this->peminjaman->fasilitas_id)
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
        $grid = collect($this->slotTersedia)->firstWhere('id', $slotSesiId);

        if ($grid === null || $grid['status'] !== 'tersedia') {
            return;
        }

        $this->slotSesiId = $slotSesiId;
    }

    public function submit(BookingService $bookingService)
    {
        $this->validate([
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'slotSesiId' => ['required', 'integer'],
            'surat' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:4096'],
        ]);

        $grid = collect($this->slotTersedia)->firstWhere('id', $this->slotSesiId);

        if ($grid === null || $grid['status'] !== 'tersedia') {
            $this->addError('slotSesiId', __('Slot yang dipilih tidak tersedia. Pilih slot lain.'));

            return;
        }

        $bookingService->revisiPengajuan($this->peminjaman, [
            'slot_sesi_id' => $this->slotSesiId,
            'tanggal' => $this->tanggal,
        ], $this->surat, auth()->user());

        session()->flash('status', __('Pengajuan berhasil direvisi. Menunggu verifikasi pengelola.'));

        return $this->redirectRoute('peminjaman.index', navigate: true);
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
            <h1 class="h4 fw-bold mb-1">{{ __('Revisi Pengajuan Peminjaman') }}</h1>
            <p class="text-secondary mb-0">{{ __('Perbaiki surat atau jadwal sesuai catatan pengelola, lalu ajukan ulang.') }}</p>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="alert alert-danger border-0 rounded-4 d-flex gap-3 mb-4">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div>
                    <div class="fw-bold mb-1">{{ __('Pesan dari pengelola:') }}</div>
                    <div>{{ $peminjaman->catatan_verifikasi }}</div>
                    <div class="small mt-2 text-danger-emphasis">
                        {{ __('Batas waktu revisi 24 jam sebelum slot dilepas otomatis.') }}
                    </div>
                </div>
            </div>

            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h6 class="text-uppercase text-secondary small fw-bold mb-3">{{ __('Fasilitas') }}</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold text-dark">{{ $peminjaman->fasilitas->nama }}</div>
                        <span class="badge bg-light text-dark border">{{ ucfirst($peminjaman->fasilitas->jenis) }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="text-uppercase text-secondary small fw-bold mb-3">{{ __('Jadwal Baru') }}</h6>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="tanggal">{{ __('Tanggal Sewa') }}</label>
                        <input id="tanggal" type="date" class="form-control @error('tanggal') is-invalid @enderror"
                               wire:model.live="tanggal" min="{{ today()->toDateString() }}">
                        @error('tanggal')
                            <div class="alert alert-danger border-0 rounded-3 small mt-2 mb-0">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('Pilih Sesi') }}</label>
                        @error('slotSesiId')
                            <div class="alert alert-danger border-0 rounded-3 small mb-2">{{ $message }}</div>
                        @enderror
                        <div class="row g-3">
                            @forelse ($this->slotTersedia as $slot)
                                <div class="col-sm-6 col-lg-4" wire:key="slot-{{ $slot['id'] }}">
                                    @if ($slot['status'] === 'tersedia')
                                        <button type="button" wire:click="pilihSlot({{ $slot['id'] }})"
                                                class="btn w-100 p-3 text-start rounded-4 shadow-sm border-2 {{ $slotSesiId === $slot['id'] ? 'btn-primary text-white' : 'btn-outline-primary' }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge {{ $slotSesiId === $slot['id'] ? 'bg-white text-primary' : 'bg-primary text-white' }} rounded-pill px-2.5">
                                                    {{ $slotSesiId === $slot['id'] ? __('Dipilih') : __('Tersedia') }}
                                                </span>
                                                <i class="bi bi-arrow-right-circle fs-5"></i>
                                            </div>
                                            <div class="fw-bold fs-6">{{ $slot['label'] }}</div>
                                        </button>
                                    @elseif ($slot['status'] === 'terisi')
                                        <div class="p-3 rounded-4 bg-light text-secondary opacity-75">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-secondary text-white rounded-pill px-2.5">{{ __('Sudah Dipesan') }}</span>
                                                <i class="bi bi-lock-fill"></i>
                                            </div>
                                            <div class="fw-bold fs-6 text-dark">{{ $slot['label'] }}</div>
                                        </div>
                                    @else
                                        <div class="p-3 border border-danger border-opacity-25 rounded-4 bg-danger-subtle text-danger opacity-75">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-danger text-white rounded-pill px-2.5">{{ __('Diblokir') }}</span>
                                                <i class="bi bi-slash-circle-fill"></i>
                                            </div>
                                            <div class="fw-bold fs-6 text-danger-emphasis">{{ $slot['label'] }}</div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-light border rounded-4 text-center py-4 text-secondary mb-0">
                                        {{ __('Belum ada slot sesi untuk fasilitas ini.') }}
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <form wire:submit="submit" class="d-grid gap-4">
                        <div>
                            <label class="form-label fw-semibold" for="surat">{{ __('Surat Peminjaman Resmi Baru (wajib)') }}</label>
                            <input id="surat" type="file" wire:model="surat" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   class="form-control @error('surat') is-invalid @enderror">
                            @error('surat')
                                <div class="alert alert-danger border-0 rounded-3 small mt-2 mb-0">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('Format: PDF/DOC/DOCX atau scan JPG/PNG. Maksimal 4MB.') }}</div>
                            <div class="mt-2">
                                <a href="{{ asset(config('gor.template_surat')) }}" download class="btn btn-outline-primary btn-sm rounded-pill">
                                    <i class="bi bi-download me-1"></i> {{ __('Unduh Template Surat Resmi (.docx)') }}
                                </a>
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4 flex-grow-1 fw-semibold shadow-sm"
                                    wire:loading.attr="disabled" wire:target="surat,submit">
                                <i class="bi bi-send-check me-1"></i> {{ __('Kirim Revisi Pengajuan') }}
                            </button>
                            <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4" wire:navigate>
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
