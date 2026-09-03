<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Peminjaman;
use App\Models\Pembayaran;
use App\Services\PaymentService;

new #[Title('Verifikasi Pembayaran')] class extends Component
{
    use WithPagination;

    public ?int $detailId = null;

    public string $catatan = '';

    public function detail(int $id): void
    {
        $this->detailId = $this->detailId === $id ? null : $id;
        $this->reset('catatan');
    }

    public function setujui(PaymentService $paymentService, int $pembayaranId): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $paymentService->verifikasi(
            Pembayaran::findOrFail($pembayaranId),
            true,
            null,
            auth()->user(),
        );

        session()->flash('status', __('Pembayaran disetujui.'));
        $this->reset('detailId', 'catatan');
    }

    public function tolak(PaymentService $paymentService, int $pembayaranId): void
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $this->validate([
            'catatan' => ['required', 'string', 'max:255'],
        ]);

        $paymentService->verifikasi(
            Pembayaran::findOrFail($pembayaranId),
            false,
            $this->catatan,
            auth()->user(),
        );

        session()->flash('status', __('Pembayaran ditolak.'));
        $this->reset('detailId', 'catatan');
    }

    public function render()
    {
        return $this->view([
            'daftarPembayaran' => Pembayaran::query()
                ->where('status', Pembayaran::MENUNGGU_VERIFIKASI)
                ->with(['peminjaman.user', 'peminjaman.fasilitas', 'peminjaman.slotSesi'])
                ->orderBy('created_at')
                ->paginate(15),
        ])->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">{{ __('Verifikasi Pembayaran') }}</h1>

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
                    <th scope="col">{{ __('Nominal') }}</th>
                    <th scope="col">{{ __('Metode') }}</th>
                    <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPembayaran as $bayar)
                    <tr wire:key="bayar-{{ $bayar->id }}">
                        <td class="fw-semibold">{{ $bayar->peminjaman->kode }}</td>
                        <td>{{ $bayar->peminjaman->user->name }}</td>
                        <td>{{ $bayar->peminjaman->fasilitas->nama }}</td>
                        <td>{{ $bayar->peminjaman->tanggal->translatedFormat('d M Y') }}</td>
                        <td>Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($bayar->metode) }}</td>
                        <td class="text-end">
                            <button wire:click="detail({{ $bayar->id }})" class="btn btn-sm btn-outline-primary">
                                {{ $detailId === $bayar->id ? __('Tutup') : __('Detail') }}
                            </button>
                        </td>
                    </tr>
                    @if ($detailId === $bayar->id)
                        <tr wire:key="detail-{{ $bayar->id }}">
                            <td colspan="7" class="bg-light">
                                <div class="row g-3 p-2">
                                    <div class="col-md-5">
                                        <h6 class="small text-uppercase text-secondary">{{ __('Bukti Pembayaran') }}</h6>
                                        @php $media = $bayar->getFirstMedia('bukti'); @endphp
                                        @if ($media)
                                            <img src="{{ route('bukti.show', $bayar) }}" alt="{{ __('Bukti pembayaran') }}"
                                                 class="img-fluid border rounded" style="max-height: 320px;">
                                        @else
                                            <div class="alert alert-light border small mb-0">{{ __('Bukti tidak tersedia.') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-7">
                                        <h6 class="small text-uppercase text-secondary">{{ __('Keputusan') }}</h6>
                                        <div class="mb-3">
                                            <label class="form-label" for="catatan-{{ $bayar->id }}">
                                                {{ __('Catatan (wajib jika menolak)') }}
                                            </label>
                                            <textarea id="catatan-{{ $bayar->id }}" class="form-control" rows="2"
                                                      wire:model="catatan"></textarea>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button wire:click="setujui({{ $bayar->id }})"
                                                    wire:confirm="{{ __('Setujui pembayaran ini?') }}"
                                                    class="btn btn-success btn-sm">{{ __('Setujui') }}</button>
                                            <button wire:click="tolak({{ $bayar->id }})"
                                                    wire:confirm="{{ __('Tolak pembayaran ini?') }}"
                                                    class="btn btn-danger btn-sm">{{ __('Tolak') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="7" class="text-center text-secondary py-4">{{ __('Tidak ada pembayaran menunggu verifikasi.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $daftarPembayaran->links() }}
</div>