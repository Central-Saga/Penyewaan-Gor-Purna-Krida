<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Peminjaman;
use App\Models\Pembayaran;
use App\Services\PaymentService;

new #[Title('Pembayaran')] class extends Component
{
    use WithFileUploads;

    public Peminjaman $peminjaman;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $bukti;

    public string $metode = 'transfer';

    public function upload(PaymentService $paymentService)
    {
        $user = auth()->user();

        if ($user === null || $this->peminjaman->user_id !== $user->id) {
            abort(403);
        }

        $this->validate([
            'bukti' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'metode' => ['required', 'in:transfer,qris'],
        ]);

        $paymentService->upload($this->peminjaman, $this->bukti, $this->metode, $user);

        session()->flash('status', __('Bukti pembayaran terkirim. Menunggu verifikasi pengelola.'));

        return $this->redirectRoute('peminjaman.index', navigate: true);
    }

    public function render()
    {
        $this->peminjaman->load(['fasilitas', 'slotSesi', 'pembayaran']);

        return $this->view()
            ->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">{{ __('Pembayaran') }} — {{ $peminjaman->kode }}</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('status')<div class="alert alert-danger">{{ $message }}</div>@enderror
    @error('bukti')<div class="alert alert-danger">{{ $message }}</div>@enderror
    @error('metode')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle text-secondary mb-2">{{ __('Ringkasan Booking') }}</h6>
                    <dl class="row mb-0">
                        <dt class="col-5">{{ __('Fasilitas') }}</dt>
                        <dd class="col-7">{{ $peminjaman->fasilitas->nama }}</dd>
                        <dt class="col-5">{{ __('Slot') }}</dt>
                        <dd class="col-7">{{ $peminjaman->slotSesi->label }}</dd>
                        <dt class="col-5">{{ __('Tanggal') }}</dt>
                        <dd class="col-7">{{ $peminjaman->tanggal->translatedFormat('d M Y') }}</dd>
                        <dt class="col-5">{{ __('Nominal') }}</dt>
                        <dd class="col-7 fw-bold fs-5">Rp {{ number_format($peminjaman->fasilitas->tarif_per_sesi, 0, ',', '.') }}</dd>
                        <dt class="col-5">{{ __('Status') }}</dt>
                        <dd class="col-7"><x-status-badge :status="$peminjaman->status" /></dd>
                    </dl>
                </div>
            </div>

            @if ($peminjaman->status === App\Models\Peminjaman::MENUNGGU_PEMBAYARAN)
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title h6">{{ __('Instruksi Pembayaran') }}</h6>
                        <p class="small text-secondary mb-2">
                            {{ __('Transfer ke rekening berikut atau pindai QRIS, lalu unggah bukti:') }}
                        </p>
                        <div class="border rounded p-3 mb-3 small">
                            <div><strong>Bank Daerah Badung</strong></div>
                            <div>No. Rek: 1234-5678-90</div>
                            <div>a.n. DISDIKPORA Kab. Badung</div>
                        </div>

                        <form wire:submit="upload" class="d-grid gap-3">
                            <div>
                                <label class="form-label" for="metode">{{ __('Metode') }}</label>
                                <select id="metode" class="form-select" wire:model="metode">
                                    <option value="transfer">{{ __('Transfer Bank') }}</option>
                                    <option value="qris">{{ __('QRIS') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label" for="bukti">{{ __('Bukti (JPG/PNG, maks 2MB)') }}</label>
                                <input id="bukti" type="file" accept="image/jpeg,image/png"
                                       class="form-control" wire:model="bukti">
                            </div>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                {{ __('Unggah Bukti') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">{{ __('Riwayat Pembayaran') }}</div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">{{ __('Waktu') }}</th>
                                <th scope="col">{{ __('Metode') }}</th>
                                <th scope="col">{{ __('Nominal') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Catatan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjaman->pembayaran()->withTrashed()->latest()->get() as $bayar)
                                <tr>
                                    <td class="small">{{ $bayar->created_at?->translatedFormat('d M Y H:i') }}</td>
                                    <td>{{ ucfirst($bayar->metode) }}</td>
                                    <td>Rp {{ number_format($bayar->nominal, 0, ',', '.') }}</td>
                                    <td><x-status-badge :status="$bayar->status" /></td>
                                    <td class="small">{{ $bayar->catatan_verifikasi ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-secondary py-4">{{ __('Belum ada pembayaran.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>