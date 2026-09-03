<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Peminjaman;
use App\Models\Pembayaran;
use App\Models\User;

new #[Title('Dashboard')] class extends Component
{
    public function render()
    {
        /** @var User $user */
        $user = auth()->user();

        $data = [
            'role' => $user->hasRole('admin') ? 'admin' : ($user->hasRole('pengelola') ? 'pengelola' : 'pengguna'),
        ];

        if ($data['role'] === 'pengguna') {
            $data['totalPeminjaman'] = Peminjaman::where('user_id', $user->id)->count();
            $data['peminjamanTerbaru'] = Peminjaman::with(['fasilitas', 'slotSesi'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        } elseif ($data['role'] === 'pengelola') {
            $data['menungguVerifikasiCount'] = Pembayaran::where('status', Pembayaran::MENUNGGU_VERIFIKASI)->count();
            $data['jadwalHariIniCount'] = Peminjaman::whereDate('tanggal', today())
                ->whereIn('status', Peminjaman::STATUS_AKTIF)
                ->count();
            $data['pemasukanHariIni'] = Pembayaran::where('status', Pembayaran::TERVERIFIKASI)
                ->whereDate('verified_at', today())
                ->sum('nominal');
            $data['jadwalHariIni'] = Peminjaman::with(['fasilitas', 'slotSesi', 'user'])
                ->whereDate('tanggal', today())
                ->whereIn('status', Peminjaman::STATUS_AKTIF)
                ->orderBy('slot_sesi_id')
                ->get();
        } else { // admin
            $data['totalPeminjaman'] = Peminjaman::count();
            $data['totalPemasukan'] = Pembayaran::where('status', Pembayaran::TERVERIFIKASI)->sum('nominal');
            $data['totalPengguna'] = User::count();
            $data['distribusiStatus'] = Peminjaman::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        }

        return $this->view($data)->layout('layouts.app');
    }
}; ?>

<div>
    {{-- Header Banner --}}
    <div class="card border-0 rounded-4 mb-4 text-white overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="card-body p-4 p-md-5 position-relative">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1.5 small mb-3">
                        <i class="bi bi-shield-check me-1"></i> {{ ucfirst($role) }} Panel
                    </span>
                    <h1 class="h2 fw-bold text-white mb-2">
                        {{ __('Selamat datang kembali, :nama!', ['nama' => auth()->user()->name]) }}
                    </h1>
                    <p class="text-light text-opacity-75 mb-0" style="max-width: 580px;">
                        @if ($role === 'admin')
                            {{ __('Pantau seluruh aktivitas pemesanan, perputaran pendapatan sewa gelanggang, serta kelola pengguna sistem GOR Purnakrida.') }}
                        @elseif ($role === 'pengelola')
                            {{ __('Kelola jadwal gelanggang, verifikasi bukti transfer pembayaran sewa, dan pantau penggunaan fasilitas hari ini.') }}
                        @else
                            {{ __('Pesan slot gelanggang olahraga di GOR Purnakrida secara langsung, unggah bukti pembayaran, dan pantau status peminjaman Anda.') }}
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    @if ($role === 'pengguna')
                        <a href="{{ route('jadwal.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold shadow" wire:navigate>
                            <i class="bi bi-calendar-plus me-1.5"></i> {{ __('Sewa Lapangan') }}
                        </a>
                    @elseif ($role === 'pengelola')
                        <a href="{{ route('verifikasi.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold shadow" wire:navigate>
                            <i class="bi bi-shield-check me-1.5"></i> {{ __('Verifikasi Bayar') }}
                        </a>
                    @else
                        <a href="{{ route('laporan.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold shadow" wire:navigate>
                            <i class="bi bi-file-earmark-bar-graph me-1.5"></i> {{ __('Buka Laporan') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($role === 'pengguna')
        {{-- Pengguna Dashboard --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Total Peminjaman Saya') }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalPeminjaman }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="stat-card-modern bg-primary-subtle border-primary border-opacity-25 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold text-primary-emphasis mb-1">{{ __('Cari Jadwal Lapangan Kosong?') }}</h6>
                        <p class="small text-secondary mb-0">{{ __('Pilih tanggal & sesi, booking secara online sebelum slot terisi penuh.') }}</p>
                    </div>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-primary rounded-pill px-4 small fw-semibold text-nowrap" wire:navigate>
                        {{ __('Lihat Kalender Jadwal') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center p-4">
                <div>
                    <h5 class="fw-bold text-dark mb-1">{{ __('Riwayat Peminjaman Terbaru') }}</h5>
                    <p class="text-secondary small mb-0">{{ __('Daftar 5 pengajuan sewa fasilitas terakhir yang Anda buat.') }}</p>
                </div>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-primary rounded-pill btn-sm px-3" wire:navigate>
                    {{ __('Lihat Semua') }}
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">{{ __('Kode Booking') }}</th>
                            <th>{{ __('Fasilitas') }}</th>
                            <th>{{ __('Tanggal & Sesi') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end pe-4">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($peminjamanTerbaru as $p)
                            <tr>
                                <td class="ps-4 fw-bold text-primary font-monospace">{{ $p->kode }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $p->fasilitas->nama }}</div>
                                    <small class="text-secondary">{{ ucfirst($p->fasilitas->jenis) }}</small>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $p->tanggal->translatedFormat('d F Y') }}</div>
                                    <small class="text-secondary">
                                        {{ $p->slotSesi->nama_sesi }} ({{ substr($p->slotSesi->jam_mulai, 0, 5) }} - {{ substr($p->slotSesi->jam_selesai, 0, 5) }} WITA)
                                    </small>
                                </td>
                                <td><x-status-badge :status="$p->status" /></td>
                                <td class="text-end pe-4">
                                    @if ($p->status === 'menunggu_pembayaran')
                                        <a href="{{ route('pembayaran.show', $p) }}" class="btn btn-sm btn-primary rounded-pill px-3" wire:navigate>
                                            <i class="bi bi-credit-card me-1"></i> {{ __('Bayar') }}
                                        </a>
                                    @else
                                        <span class="text-secondary small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">
                                    <i class="bi bi-inbox fs-2 text-muted mb-2 d-block"></i>
                                    {{ __('Belum ada riwayat peminjaman fasilitas.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif ($role === 'pengelola')
        {{-- Pengelola Dashboard --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Menunggu Verifikasi') }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $menungguVerifikasiCount }}</h3>
                        <a href="{{ route('verifikasi.index') }}" class="small text-decoration-none fw-semibold text-primary d-inline-block mt-1" wire:navigate>
                            {{ __('Buka Verifikasi →') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-calendar2-week"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Jadwal Hari Ini') }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $jadwalHariIniCount }}</h3>
                        <span class="small text-secondary mt-1 d-inline-block">{{ __('Slot sewa aktif hari ini') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Pemasukan Hari Ini') }}</span>
                        <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}</h3>
                        <span class="small text-secondary mt-1 d-inline-block">{{ __('Terverifikasi hari ini') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-transparent border-0 p-4">
                <h5 class="fw-bold text-dark mb-1">
                    {{ __('Jadwal Gelanggang Hari Ini (:tgl)', ['tgl' => today()->translatedFormat('d F Y')]) }}
                </h5>
                <p class="text-secondary small mb-0">{{ __('Daftar slot yang sedang atau akan digunakan oleh penyewa hari ini.') }}</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">{{ __('Kode Booking') }}</th>
                            <th>{{ __('Fasilitas') }}</th>
                            <th>{{ __('Sesi / Jam') }}</th>
                            <th>{{ __('Penyewa') }}</th>
                            <th>{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jadwalHariIni as $j)
                            <tr>
                                <td class="ps-4 fw-bold text-primary font-monospace">{{ $j->kode }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $j->fasilitas->nama }}</div>
                                    <small class="text-secondary">{{ ucfirst($j->fasilitas->jenis) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5">
                                        {{ $j->slotSesi->nama_sesi }} ({{ substr($j->slotSesi->jam_mulai, 0, 5) }} - {{ substr($j->slotSesi->jam_selesai, 0, 5) }} WITA)
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark">{{ $j->user->name }}</div>
                                    <small class="text-secondary">{{ $j->user->no_hp ?? 'No. HP belum diisi' }}</small>
                                </td>
                                <td><x-status-badge :status="$j->status" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">
                                    <i class="bi bi-calendar-x fs-2 text-muted mb-2 d-block"></i>
                                    {{ __('Tidak ada jadwal peminjaman lapangan hari ini.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- Admin Dashboard --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Total Peminjaman') }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalPeminjaman }}</h3>
                        <span class="small text-secondary mt-1 d-inline-block">{{ __('Seluruh riwayat booking') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Total Pemasukan') }}</span>
                        <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                        <span class="small text-secondary mt-1 d-inline-block">{{ __('Pembayaran terverifikasi') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Pengguna Terdaftar') }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $totalPengguna }}</h3>
                        <a href="{{ route('panel.pengguna.index') }}" class="small text-decoration-none fw-semibold text-primary d-inline-block mt-1" wire:navigate>
                            {{ __('Kelola Pengguna →') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-header bg-transparent border-0 p-4 pb-2">
                <h5 class="fw-bold text-dark mb-1">{{ __('Distribusi Status Peminjaman') }}</h5>
                <p class="text-secondary small mb-0">{{ __('Ringkasan volume transaksi sewa berdasarkan alur status peminjaman.') }}</p>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="row g-3">
                    @php
                        $statusMeta = [
                            'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'icon' => 'bi-hourglass-split', 'class' => 'warning'],
                            'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'icon' => 'bi-shield-exclamation', 'class' => 'info'],
                            'disetujui' => ['label' => 'Disetujui', 'icon' => 'bi-check2-circle', 'class' => 'success'],
                            'dibatalkan' => ['label' => 'Dibatalkan', 'icon' => 'bi-x-circle', 'class' => 'danger'],
                            'selesai' => ['label' => 'Selesai', 'icon' => 'bi-flag-fill', 'class' => 'secondary'],
                        ];
                    @endphp

                    @foreach ($statusMeta as $st => $meta)
                        <div class="col-md">
                            <div class="p-3 border rounded-3 bg-light d-flex flex-column justify-content-between h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-semibold text-secondary">{{ $meta['label'] }}</span>
                                    <i class="bi {{ $meta['icon'] }} text-{{ $meta['class'] }} fs-5"></i>
                                </div>
                                <h3 class="fw-bold text-dark mb-0">{{ $distribusiStatus[$st] ?? 0 }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
