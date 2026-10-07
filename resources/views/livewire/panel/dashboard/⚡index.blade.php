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
            $data['menungguVerifikasiCount'] = Peminjaman::where('status', Peminjaman::MENUNGGU_VERIFIKASI)->count();
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
            $data['okupansiHariIni'] = $data['jadwalHariIni']->groupBy('fasilitas_id');
            $data['semuaFasilitas'] = \App\Models\Fasilitas::orderBy('nama')->get();
        } else { // admin
            $data['totalPeminjaman'] = Peminjaman::count();
            $data['totalPemasukan'] = Pembayaran::where('status', Pembayaran::TERVERIFIKASI)->sum('nominal');
            $data['totalPengguna'] = User::count();

            // Ringkasan periode.
            $data['pemasukanHariIni'] = Pembayaran::where('status', Pembayaran::TERVERIFIKASI)
                ->whereDate('verified_at', today())
                ->sum('nominal');
            $data['pemasukanBulanIni'] = Pembayaran::where('status', Pembayaran::TERVERIFIKASI)
                ->whereYear('verified_at', today()->year)
                ->whereMonth('verified_at', today()->month)
                ->sum('nominal');
            $data['peminjamanHariIni'] = Peminjaman::whereDate('tanggal', today())
                ->whereIn('status', Peminjaman::STATUS_AKTIF)
                ->count();

            $data['distribusiStatus'] = Peminjaman::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();

            // Tren pemasukan terverifikasi 6 bulan terakhir (untuk grafik batang).
            $tren = [];
            for ($i = 5; $i >= 0; $i--) {
                $bulan = today()->copy()->subMonthsNoOverflow($i);
                $tren[] = [
                    'label' => $bulan->translatedFormat('M'),
                    'labelPanjang' => $bulan->translatedFormat('F Y'),
                    'total' => (float) Pembayaran::where('status', Pembayaran::TERVERIFIKASI)
                        ->whereYear('verified_at', $bulan->year)
                        ->whereMonth('verified_at', $bulan->month)
                        ->sum('nominal'),
                ];
            }
            $data['trenPemasukan'] = $tren;
            $data['trenMaks'] = max(array_column($tren, 'total')) ?: 1;

            // Okupansi per lapangan hari ini (peminjaman aktif).
            $data['okupansiHariIni'] = \App\Models\Fasilitas::query()
                ->withCount(['peminjaman as aktif_hari_ini' => fn ($q) => $q
                    ->whereDate('tanggal', today())
                    ->whereIn('status', Peminjaman::STATUS_AKTIF)])
                ->withCount('slotSesi')
                ->orderBy('nama')
                ->get();

            // Feed aktivitas terbaru.
            $data['aktivitasTerbaru'] = \Spatie\Activitylog\Models\Activity::query()
                ->with('causer')
                ->latest()
                ->take(8)
                ->get();

            $data['statistikFasilitas'] = app(\App\Services\LaporanService::class)->statistikFasilitas();
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
                            {{ __('Verifikasi pengajuan jadwal & surat resmi penyewa, dan pantau penggunaan fasilitas hari ini.') }}
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
                            <i class="bi bi-shield-check me-1.5"></i> {{ __('Verifikasi Pengajuan') }}
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
        <div class="bg-primary-subtle border border-primary border-opacity-25 rounded-4 p-4 d-flex flex-wrap gap-3 align-items-center mb-4">
            <div class="flex-grow-1">
                <h6 class="fw-bold text-primary-emphasis mb-1">
                    <i class="bi bi-file-earmark-text me-1"></i> {{ __('Template Surat Peminjaman Resmi') }}
                </h6>
                <p class="text-secondary small mb-0">
                    {{ __('Surat resmi berkop instansi wajib diunggah saat mengajukan jadwal sewa. Ganti semua kalimat di dalam tanda kurung pada template sesuai data kegiatan Anda.') }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ asset(config('gor.template_surat')) }}" download class="btn btn-primary rounded-pill">
                    <i class="bi bi-download me-1"></i> {{ __('Unduh Template (.docx)') }}
                </a>
                <a href="{{ route('panduan') }}#alur" class="btn btn-outline-primary rounded-pill" target="_blank">
                    <i class="bi bi-list-ol me-1"></i> {{ __('Tata Cara Menyewa') }}
                </a>
            </div>
        </div>

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
                                        {{ $p->slotSesi->nama }} ({{ substr($p->slotSesi->jam_mulai, 0, 5) }} - {{ substr($p->slotSesi->jam_selesai, 0, 5) }} WITA)
                                    </small>
                                </td>
                                <td><x-status-badge :status="$p->status" /></td>
                                <td class="text-end pe-4">
                                    @if ($p->status === 'menunggu_pembayaran')
                                        <a href="{{ route('pembayaran.show', $p) }}" class="btn btn-sm btn-primary rounded-pill px-3" wire:navigate>
                                            <i class="bi bi-credit-card me-1"></i> {{ __('Bayar') }}
                                        </a>
                                    @elseif ($p->status === 'ditolak')
                                        <a href="{{ route('peminjaman.revisi', $p) }}" class="btn btn-sm btn-warning rounded-pill px-3" wire:navigate>
                                            <i class="bi bi-pencil-square me-1"></i> {{ __('Revisi') }}
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
                                        {{ $j->slotSesi->nama }} ({{ substr($j->slotSesi->jam_mulai, 0, 5) }} - {{ substr($j->slotSesi->jam_selesai, 0, 5) }} WITA)
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

        {{-- Ringkasan periode --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Pemasukan Hari Ini') }}</span>
                        <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($pemasukanHariIni, 0, ',', '.') }}</h3>
                        <span class="small text-secondary mt-1 d-inline-block">{{ today()->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Pemasukan Bulan Ini') }}</span>
                        <h3 class="fw-bold mb-0 text-primary-emphasis">Rp {{ number_format($pemasukanBulanIni, 0, ',', '.') }}</h3>
                        <span class="small text-secondary mt-1 d-inline-block">{{ today()->translatedFormat('F Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card-modern d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-calendar2-check"></i>
                    </div>
                    <div>
                        <span class="text-secondary small fw-semibold text-uppercase">{{ __('Jadwal Aktif Hari Ini') }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ $peminjamanHariIni }}</h3>
                        <span class="small text-secondary mt-1 d-inline-block">{{ __('Slot terkunci hari ini') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tren pemasukan + okupansi --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 p-4 pb-2">
                        <h5 class="fw-bold text-dark mb-1">{{ __('Tren Pemasukan 6 Bulan') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('Total pembayaran terverifikasi per bulan.') }}</p>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="d-flex align-items-end justify-content-between gap-2" style="height: 200px;">
                            @foreach ($trenPemasukan as $t)
                                @php $persen = (int) round(($t['total'] / $trenMaks) * 100); @endphp
                                <div class="d-flex flex-column align-items-center justify-content-end h-100 flex-fill"
                                     title="{{ $t['labelPanjang'] }}: Rp {{ number_format($t['total'], 0, ',', '.') }}">
                                    <div class="small text-secondary mb-1 text-nowrap" style="font-size: 0.7rem;">
                                        Rp {{ number_format($t['total'] / 1000, 0, ',', '.') }}rb
                                    </div>
                                    <div class="w-100 rounded-top-3 bg-primary bg-gradient"
                                         style="height: {{ max($persen, 2) }}%; min-height: 4px; transition: height .3s;"
                                         role="img" aria-label="{{ $t['labelPanjang'] }}"></div>
                                    <div class="small fw-semibold text-dark mt-2">{{ $t['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 rounded-4 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 p-4 pb-2">
                        <h5 class="fw-bold text-dark mb-1">{{ __('Okupansi Lapangan Hari Ini') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('Slot aktif dibanding total slot tiap lapangan.') }}</p>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @forelse ($okupansiHariIni as $f)
                            @php
                                $totalSlot = max($f->slot_sesi_count, 1);
                                $persen = (int) round(($f->aktif_hari_ini / $totalSlot) * 100);
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold text-dark">{{ $f->nama }}</span>
                                    <span class="text-secondary">{{ $f->aktif_hari_ini }}/{{ $f->slot_sesi_count }} slot</span>
                                </div>
                                <div class="progress" style="height: 8px;" role="progressbar" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary" style="width: {{ $persen }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-secondary py-4">{{ __('Belum ada lapangan terdaftar.') }}</div>
                        @endforelse
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
                            'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'icon' => 'bi-shield-exclamation', 'class' => 'info'],
                            'ditolak' => ['label' => 'Perlu Revisi', 'icon' => 'bi-exclamation-triangle', 'class' => 'danger'],
                            'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran', 'icon' => 'bi-hourglass-split', 'class' => 'warning'],
                            'disetujui' => ['label' => 'Tersewa', 'icon' => 'bi-check2-circle', 'class' => 'success'],
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

    <div class="card border-0 rounded-4 shadow-sm mt-4">
        <div class="card-header bg-transparent border-0 p-4 pb-2">
            <h5 class="fw-bold text-dark mb-1">{{ __('Statistik Per Lapangan') }}</h5>
            <p class="text-secondary small mb-0">{{ __('Total peminjaman, peminjaman aktif, dan pendapatan terverifikasi tiap fasilitas.') }}</p>
        </div>
        <div class="card-body p-4 pt-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Fasilitas') }}</th>
                            <th class="text-center">{{ __('Total Peminjaman') }}</th>
                            <th class="text-center">{{ __('Peminjaman Aktif') }}</th>
                            <th class="text-end">{{ __('Pendapatan Terverifikasi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($statistikFasilitas as $stat)
                            <tr>
                                <td class="fw-semibold">{{ $stat->nama }}</td>
                                <td class="text-center">{{ $stat->total_peminjaman }}</td>
                                <td class="text-center">{{ $stat->peminjaman_aktif }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($stat->pendapatan_terverifikasi, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">{{ __('Belum ada fasilitas terdaftar.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-0 rounded-4 shadow-sm mt-4">
        <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">{{ __('Aktivitas Terbaru') }}</h5>
                <p class="text-secondary small mb-0">{{ __('Jejak perubahan status & pengelolaan fasilitas terakhir.') }}</p>
            </div>
            <a href="{{ route('activity.logs.index') }}" class="btn btn-outline-primary rounded-pill btn-sm px-3" wire:navigate>
                {{ __('Lihat Semua') }}
            </a>
        </div>
        <div class="card-body p-4 pt-2">
            <div class="list-group list-group-flush">
                @forelse ($aktivitasTerbaru as $log)
                    <div class="list-group-item px-0 py-3 d-flex align-items-start gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span class="fw-semibold text-dark">{{ $log->description ?: ucfirst($log->event ?? 'aktivitas') }}</span>
                                <span class="badge bg-light text-dark border">{{ $log->log_name }}</span>
                            </div>
                            <div class="small text-secondary">
                                {{ $log->causer?->name ?? __('Sistem') }}
                                &middot; {{ $log->created_at?->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-secondary py-4">{{ __('Belum ada aktivitas tercatat.') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    @endif
</div>
