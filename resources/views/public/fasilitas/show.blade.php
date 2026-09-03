<x-layouts.public :title="$fasilitas->nama . ' - GOR Purnakrida'">
    {{-- Header / Breadcrumb --}}
    <div class="bg-dark text-white py-4 border-bottom border-secondary border-opacity-25" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none" wire:navigate>{{ __('Beranda') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fasilitas.public') }}" class="text-white-50 text-decoration-none" wire:navigate>{{ __('Fasilitas') }}</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $fasilitas->nama }}</li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h1 class="h2 fw-bold text-white mb-1">{{ $fasilitas->nama }}</h1>
                    <div class="d-flex align-items-center gap-2 text-light text-opacity-75 small">
                        <span><i class="bi bi-geo-alt text-info"></i> Area {{ ucfirst($fasilitas->jenis) }}</span>
                        <span>•</span>
                        <span><i class="bi bi-people-fill text-warning"></i> Kapasitas {{ $fasilitas->kapasitas }} Orang</span>
                        <span>•</span>
                        <span class="badge bg-success rounded-pill px-2.5">Tersedia</span>
                    </div>
                </div>
                <div>
                    @auth
                        <a href="{{ route('jadwal.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" wire:navigate>
                            <i class="bi bi-calendar-check"></i>
                            <span>{{ __('Pilih Jadwal & Sewa') }}</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" wire:navigate>
                            <i class="bi bi-person-plus"></i>
                            <span>{{ __('Daftar untuk Menyewa') }}</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                {{-- Left: Visual & Details --}}
                <div class="col-lg-8">
                    {{-- Big Photo Card --}}
                    <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-4 bg-white">
                        <div class="position-relative" style="height: 420px;">
                            <img src="{{ $fasilitas->foto_url }}" alt="{{ $fasilitas->nama }}" class="w-100 h-100 object-fit-cover">
                            <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 100%);">
                                <span class="badge bg-primary rounded-pill px-3 py-1.5 shadow-sm">
                                    {{ ucfirst($fasilitas->jenis) }} Court
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <h4 class="fw-bold text-dark mb-3">{{ __('Deskripsi Fasilitas') }}</h4>
                            <p class="text-secondary lh-lg mb-4">
                                {{ $fasilitas->deskripsi }}
                            </p>

                            <hr class="my-4 text-muted opacity-25">

                            <h5 class="fw-bold text-dark mb-3">{{ __('Spesifikasi & Fasilitas Penunjang') }}</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-body-tertiary">
                                        <div class="rounded-3 bg-primary bg-opacity-10 p-2 text-primary">
                                            <i class="bi bi-layers-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Tipe Lantai</div>
                                            <div class="fw-semibold text-dark">{{ $fasilitas->jenis === 'indoor' ? 'Mat Synthetic Standar' : 'Semen Halus Standar' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-body-tertiary">
                                        <div class="rounded-3 bg-info bg-opacity-10 p-2 text-info">
                                            <i class="bi bi-lightbulb-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Penerangan</div>
                                            <div class="fw-semibold text-dark">LED Arena Standar Turnamen</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-body-tertiary">
                                        <div class="rounded-3 bg-success bg-opacity-10 p-2 text-success">
                                            <i class="bi bi-people-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Daya Tampung</div>
                                            <div class="fw-semibold text-dark">Hingga {{ $fasilitas->kapasitas }} Orang</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-body-tertiary">
                                        <div class="rounded-3 bg-warning bg-opacity-10 p-2 text-warning">
                                            <i class="bi bi-shield-check fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="text-muted small">Kelayakan</div>
                                            <div class="fw-semibold text-dark">Latihan & Pertandingan Resmi</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-3 bg-light border border-secondary border-opacity-10">
                                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-exclamation-circle text-primary me-1"></i> Catatan Penggunaan</h6>
                                <p class="small text-secondary mb-0">
                                    Penyewa wajib memakai sepatu khusus olahraga yang tidak meninggalkan bekas (non-marking shoes untuk area indoor). Dilarang membawa makanan berlemak dan minuman selain air mineral ke dalam area permainan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Sidebar Pricing & Action Card --}}
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white sticky-top" style="top: 100px;">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold text-uppercase small align-self-start mb-3">
                            {{ __('Tarif Resmi DISDIKPORA') }}
                        </span>

                        <div class="mb-4">
                            <span class="text-muted small d-block mb-1">{{ __('Biaya Sewa per Sesi') }}</span>
                            <div class="display-6 fw-bold text-primary">
                                Rp {{ number_format($fasilitas->tarif_per_sesi, 0, ',', '.') }}
                            </div>
                            <span class="text-secondary small">{{ __('Tarif standar sesuai peraturan daerah') }}</span>
                        </div>

                        <ul class="list-unstyled d-flex flex-column gap-2.5 small text-secondary mb-4 pb-3 border-bottom">
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check2 text-success fs-5"></i>
                                <span>Durasi per slot sesi terjadwal</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check2 text-success fs-5"></i>
                                <span>Akses tribun dan fasilitas umum</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-check2 text-success fs-5"></i>
                                <span>Konfirmasi booking daring cepat</span>
                            </li>
                        </ul>

                        <div class="d-grid gap-2 mb-3">
                            @auth
                                <a href="{{ route('jadwal.index') }}" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm" wire:navigate>
                                    <i class="bi bi-calendar2-check me-1"></i> {{ __('Pilih Slot di Jadwal') }}
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm" wire:navigate>
                                    <i class="bi bi-person-plus me-1"></i> {{ __('Daftar Akun Pengguna') }}
                                </a>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary rounded-pill py-2.5 fw-medium" wire:navigate>
                                    {{ __('Masuk Akun') }}
                                </a>
                            @endauth
                        </div>

                        <div class="text-center">
                            <a href="{{ route('panduan') }}" class="small text-muted text-decoration-none" wire:navigate>
                                <i class="bi bi-info-circle me-1"></i> {{ __('Lihat alur cara sewa & tata tertib') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fasilitas Lainnya --}}
            @if ($fasilitasLain->isNotEmpty())
                <div class="mt-5 pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="fw-bold text-dark mb-0">{{ __('Fasilitas Lainnya di GOR Purnakrida') }}</h4>
                        <a href="{{ route('fasilitas.public') }}" class="btn btn-sm btn-link text-primary text-decoration-none fw-semibold" wire:navigate>
                            {{ __('Lihat Semua') }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="row g-4">
                        @foreach ($fasilitasLain as $fl)
                            <div class="col-sm-6 col-lg-4">
                                <x-fasilitas-card :fasilitas="$fl" :action="auth()->check() ? route('jadwal.index') : route('register')" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layouts.public>
