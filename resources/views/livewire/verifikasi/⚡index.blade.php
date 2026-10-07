<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Peminjaman;
use App\Services\BookingService;

new #[Title('Verifikasi Pengajuan')] class extends Component
{
    use WithPagination;

    public ?int $detailId = null;

    public string $catatan = '';

    public function detail(int $id): void
    {
        $this->detailId = $this->detailId === $id ? null : $id;
        $this->catatan = '';
    }

    public function setujui(BookingService $bookingService, int $id): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $bookingService->setujuiPengajuan(Peminjaman::findOrFail($id), auth()->user());

        $this->detailId = null;
        session()->flash('status', __('Pengajuan disetujui. Penyewa diminta melakukan pembayaran.'));
    }

    public function tolak(BookingService $bookingService, int $id): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $this->validate([
            'catatan' => ['required', 'string', 'max:255'],
        ], attributes: [
            'catatan' => __('catatan penolakan'),
        ]);

        $bookingService->tolakPengajuan(Peminjaman::findOrFail($id), $this->catatan, auth()->user());

        $this->detailId = null;
        $this->catatan = '';
        session()->flash('status', __('Pengajuan ditolak. Pesan revisi dikirim ke penyewa.'));
    }

    public function batalkan(BookingService $bookingService, int $id): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $bookingService->transisi(
            Peminjaman::findOrFail($id),
            Peminjaman::DIBATALKAN,
            __('Dibatalkan oleh pengelola'),
            auth()->user(),
        );

        $this->detailId = null;
        session()->flash('status', __('Pengajuan dibatalkan. Slot dilepas.'));
    }

    public function render()
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        return $this->view([
            'daftarPengajuan' => Peminjaman::query()
                ->where('status', Peminjaman::MENUNGGU_VERIFIKASI)
                ->with(['user', 'fasilitas', 'slotSesi'])
                ->orderBy('created_at')
                ->paginate(15),
        ])->layout('layouts.app');
    }
}; ?>

<div>
    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">{{ __('Verifikasi Pengajuan Peminjaman') }}</h1>
        <p class="text-secondary mb-0">{{ __('Periksa surat resmi serta jadwal/sesi yang diajukan penyewa.') }}</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('catatan')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">{{ __('Kode') }}</th>
                    <th scope="col">{{ __('Penyewa') }}</th>
                    <th scope="col">{{ __('Fasilitas') }}</th>
                    <th scope="col">{{ __('Tanggal') }}</th>
                    <th scope="col">{{ __('Sesi') }}</th>
                    <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPengajuan as $pengajuan)
                    <tr wire:key="pengajuan-{{ $pengajuan->id }}">
                        <td class="fw-semibold">{{ $pengajuan->kode }}</td>
                        <td>{{ $pengajuan->user->name }}</td>
                        <td>{{ $pengajuan->fasilitas->nama }}</td>
                        <td>{{ $pengajuan->tanggal->translatedFormat('d M Y') }}</td>
                        <td class="small">
                            {{ $pengajuan->slotSesi->nama }}<br>
                            <span class="text-secondary">({{ substr($pengajuan->slotSesi->jam_mulai, 0, 5) }} - {{ substr($pengajuan->slotSesi->jam_selesai, 0, 5) }} WITA)</span>
                        </td>
                        <td class="text-end">
                            <button wire:click="detail({{ $pengajuan->id }})" class="btn btn-sm btn-outline-primary">
                                {{ $detailId === $pengajuan->id ? __('Tutup') : __('Detail') }}
                            </button>
                        </td>
                    </tr>
                    @if ($detailId === $pengajuan->id)
                        <tr wire:key="detail-{{ $pengajuan->id }}">
                            <td colspan="6" class="bg-light">
                                <div class="row g-3 p-2">
                                    <div class="col-md-5">
                                        <h6 class="small text-uppercase text-secondary">{{ __('Surat Peminjaman') }}</h6>
                                        @if ($pengajuan->getFirstMedia('surat_peminjaman'))
                                            <a href="{{ route('surat.show', $pengajuan) }}" target="_blank"
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-file-earmark-arrow-down me-1"></i> {{ __('Lihat / Unduh Surat') }}
                                            </a>
                                        @else
                                            <div class="alert alert-warning border-0 rounded-3 small mb-0">
                                                {{ __('Surat tidak tersedia.') }}
                                            </div>
                                        @endif

                                        <div class="small text-secondary mt-3">
                                            <div><strong>{{ __('Penyewa') }}:</strong> {{ $pengajuan->user->name }}</div>
                                            <div><strong>{{ __('Kontak') }}:</strong> {{ $pengajuan->user->no_hp ?? $pengajuan->user->email }}</div>
                                            <div><strong>{{ __('Diajukan') }}:</strong> {{ $pengajuan->created_at->translatedFormat('d M Y H:i') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <h6 class="small text-uppercase text-secondary">{{ __('Keputusan') }}</h6>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold" for="catatan-{{ $pengajuan->id }}">
                                                {{ __('Catatan penolakan (wajib jika menolak)') }}
                                            </label>
                                            <textarea id="catatan-{{ $pengajuan->id }}" class="form-control" rows="2"
                                                      wire:model="catatan"
                                                      placeholder="{{ __('Contoh: Surat belum bertanda tangan, mohon revisi.') }}"></textarea>
                                            @error('catatan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button wire:click="setujui({{ $pengajuan->id }})"
                                                    wire:confirm="{{ __('Setujui pengajuan ini?') }}"
                                                    class="btn btn-success btn-sm">
                                                <i class="bi bi-check2-circle me-1"></i> {{ __('Setujui') }}
                                            </button>
                                            <button wire:click="tolak({{ $pengajuan->id }})"
                                                    wire:confirm="{{ __('Tolak pengajuan ini? Penyewa diminta merevisi.') }}"
                                                    class="btn btn-danger btn-sm">
                                                <i class="bi bi-x-circle me-1"></i> {{ __('Tolak') }}
                                            </button>
                                            <button wire:click="batalkan({{ $pengajuan->id }})"
                                                    wire:confirm="{{ __('Batalkan pengajuan ini? Slot akan dilepas.') }}"
                                                    class="btn btn-outline-danger btn-sm">
                                                {{ __('Batalkan') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="6" class="text-center text-secondary py-4">{{ __('Tidak ada pengajuan yang menunggu verifikasi.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $daftarPengajuan->links() }}
</div>
