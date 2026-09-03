<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Services\LaporanService;

new #[Title('Laporan')] class extends Component
{
    public string $jenis = 'peminjaman'; // 'peminjaman' | 'pemasukan'
    public string $mulai = '';
    public string $sampai = '';

    public function mount(): void
    {
        $this->mulai = now()->startOfMonth()->toDateString();
        $this->sampai = now()->endOfMonth()->toDateString();
    }

    public function render(LaporanService $laporanService)
    {
        if (! auth()->user()?->hasRole('admin')) {
            abort(403);
        }

        $data = [
            'jenis' => $this->jenis,
            'mulai' => $this->mulai,
            'sampai' => $this->sampai,
        ];

        if ($this->jenis === 'peminjaman') {
            $data['laporanPeminjaman'] = $laporanService->peminjaman($this->mulai, $this->sampai);
        } else {
            $pemasukan = $laporanService->pemasukan($this->mulai, $this->sampai);
            $data['laporanPemasukan'] = $pemasukan;
            $data['totalPemasukan'] = $pemasukan->sum('nominal');
        }

        return $this->view($data)->layout('layouts.app');
    }
}; ?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">{{ __('Laporan') }}</h1>
            <p class="text-secondary mb-0">{{ __('Rekap data peminjaman dan pendapatan sewa fasilitas.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.export', ['jenis' => $jenis, 'format' => 'csv', 'mulai' => $mulai, 'sampai' => $sampai]) }}"
               class="btn btn-outline-success">
                {{ __('Export CSV') }}
            </a>
            <a href="{{ route('laporan.export', ['jenis' => $jenis, 'format' => 'pdf', 'mulai' => $mulai, 'sampai' => $sampai]) }}"
               class="btn btn-danger">
                {{ __('Export PDF') }}
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form wire:submit.prevent="$refresh" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-secondary">{{ __('Jenis Laporan') }}</label>
                    <select class="form-select" wire:model.live="jenis">
                        <option value="peminjaman">{{ __('Laporan Peminjaman (Penggunaan Slot)') }}</option>
                        <option value="pemasukan">{{ __('Laporan Pemasukan (Pendapatan Terverifikasi)') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary">{{ __('Mulai Tanggal') }}</label>
                    <input type="date" class="form-control" wire:model.live="mulai">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary">{{ __('Sampai Tanggal') }}</label>
                    <input type="date" class="form-control" wire:model.live="sampai">
                </div>
            </form>
        </div>
    </div>

    @if ($jenis === 'peminjaman')
        {{-- Rekap Ringkas --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent py-3 border-0">
                        <h6 class="fw-bold mb-0">{{ __('Peminjaman Berdasarkan Status') }}</h6>
                    </div>
                    <div class="card-body pt-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($laporanPeminjaman['perStatus'] as $st => $cnt)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <x-status-badge :status="$st" />
                                    <span class="fw-bold">{{ $cnt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-transparent py-3 border-0">
                        <h6 class="fw-bold mb-0">{{ __('Peminjaman Berdasarkan Fasilitas') }}</h6>
                    </div>
                    <div class="card-body pt-0">
                        <ul class="list-group list-group-flush">
                            @foreach ($laporanPeminjaman['perFasilitas'] as $fasilitasNama => $cnt)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $fasilitasNama }}</span>
                                    <span class="fw-bold">{{ $cnt }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Detail --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-transparent py-3 border-0">
                <h6 class="fw-bold mb-0">{{ __('Daftar Riwayat Peminjaman (:total data)', ['total' => $laporanPeminjaman['daftar']->count()]) }}</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Kode') }}</th>
                            <th>{{ __('Penyewa') }}</th>
                            <th>{{ __('Fasilitas') }}</th>
                            <th>{{ __('Tanggal & Sesi') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanPeminjaman['daftar'] as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->kode }}</td>
                                <td>{{ $p->user->name }}</td>
                                <td>{{ $p->fasilitas->nama }}</td>
                                <td>{{ $p->tanggal->translatedFormat('d M Y') }} ({{ $p->slotSesi->nama_sesi }})</td>
                                <td><x-status-badge :status="$p->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">{{ __('Tidak ada data pada periode ini.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- Laporan Pemasukan --}}
        <div class="card shadow-sm border-0 mb-4 bg-success-subtle text-success-emphasis">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="small text-uppercase fw-semibold mb-1">{{ __('Total Pendapatan Terverifikasi') }}</h6>
                    <h2 class="h3 fw-bold mb-0">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h2>
                </div>
                <div class="text-end">
                    <span class="badge bg-success fs-6">{{ $laporanPemasukan->count() }} {{ __('Transaksi') }}</span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Kode Booking') }}</th>
                            <th>{{ __('Tgl Verifikasi') }}</th>
                            <th>{{ __('Penyewa') }}</th>
                            <th>{{ __('Fasilitas') }}</th>
                            <th>{{ __('Metode') }}</th>
                            <th>{{ __('Nominal') }}</th>
                            <th>{{ __('Diverifikasi Oleh') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanPemasukan as $pem)
                            <tr>
                                <td class="fw-semibold">{{ $pem->peminjaman->kode }}</td>
                                <td>{{ $pem->verified_at?->translatedFormat('d M Y H:i') }}</td>
                                <td>{{ $pem->peminjaman->user->name }}</td>
                                <td>{{ $pem->peminjaman->fasilitas->nama }}</td>
                                <td>{{ ucfirst($pem->metode) }}</td>
                                <td class="fw-bold">Rp {{ number_format($pem->nominal, 0, ',', '.') }}</td>
                                <td>{{ $pem->verifikator?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-secondary py-4">{{ __('Tidak ada data pemasukan pada periode ini.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
