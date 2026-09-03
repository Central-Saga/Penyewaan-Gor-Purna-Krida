<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pembayaran;

new #[Title('Data Transaksi')] class extends Component
{
    use WithPagination;

    public string $tanggalMulai = '';
    public string $tanggalSampai = '';
    public string $status = '';
    public string $metode = '';

    public function updating(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'pengelola'])) {
            abort(403);
        }

        $query = Pembayaran::query()
            ->with(['peminjaman.user', 'peminjaman.fasilitas', 'peminjaman.slotSesi', 'verifikator'])
            ->when(filled($this->status), fn ($q) => $q->where('status', $this->status))
            ->when(filled($this->metode), fn ($q) => $q->where('metode', $this->metode))
            ->when(filled($this->tanggalMulai), fn ($q) => $q->whereDate('created_at', '>=', $this->tanggalMulai))
            ->when(filled($this->tanggalSampai), fn ($q) => $q->whereDate('created_at', '<=', $this->tanggalSampai))
            ->latest('created_at');

        return $this->view([
            'daftarTransaksi' => $query->paginate(15),
            'totalNominal' => (clone $query)->sum('nominal'),
        ])->layout('layouts.app');
    }
}; ?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ __('Data Transaksi') }}</h1>
            <p class="text-secondary mb-0">{{ __('Riwayat seluruh transaksi pembayaran sewa fasilitas.') }}</p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form wire:submit.prevent="$refresh" class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small text-secondary">{{ __('Mulai Tanggal') }}</label>
                    <input type="date" class="form-control form-control-sm" wire:model.live="tanggalMulai">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary">{{ __('Sampai Tanggal') }}</label>
                    <input type="date" class="form-control form-control-sm" wire:model.live="tanggalSampai">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary">{{ __('Status') }}</label>
                    <select class="form-select form-select-sm" wire:model.live="status">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="menunggu_verifikasi">{{ __('Menunggu Verifikasi') }}</option>
                        <option value="terverifikasi">{{ __('Terverifikasi') }}</option>
                        <option value="ditolak">{{ __('Ditolak') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary">{{ __('Metode') }}</label>
                    <select class="form-select form-select-sm" wire:model.live="metode">
                        <option value="">{{ __('Semua Metode') }}</option>
                        <option value="transfer">{{ __('Transfer') }}</option>
                        <option value="qris">{{ __('QRIS') }}</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">{{ __('Kode Booking') }}</th>
                    <th scope="col">{{ __('Tanggal Transaksi') }}</th>
                    <th scope="col">{{ __('Penyewa') }}</th>
                    <th scope="col">{{ __('Fasilitas') }}</th>
                    <th scope="col">{{ __('Metode') }}</th>
                    <th scope="col">{{ __('Nominal') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col">{{ __('Verifikator') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarTransaksi as $t)
                    <tr>
                        <td class="fw-semibold">{{ $t->peminjaman->kode }}</td>
                        <td>{{ $t->created_at->translatedFormat('d M Y H:i') }}</td>
                        <td>{{ $t->peminjaman->user->name }}</td>
                        <td>{{ $t->peminjaman->fasilitas->nama }}</td>
                        <td>{{ ucfirst($t->metode) }}</td>
                        <td class="fw-semibold">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$t->status" /></td>
                        <td class="small text-secondary">
                            @if ($t->verifikator)
                                {{ $t->verifikator->name }}<br>
                                <span class="text-muted">{{ $t->verified_at?->translatedFormat('d M Y H:i') }}</span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">
                            {{ __('Tidak ada data transaksi yang sesuai filter.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $daftarTransaksi->links() }}
</div>
