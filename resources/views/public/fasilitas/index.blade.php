<x-layouts.public title="Katalog Fasilitas Olahraga - GOR Purnakrida">
    {{-- Page Header --}}
    <header class="bg-dark text-white py-5 position-relative" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none" wire:navigate>{{ __('Beranda') }}</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ __('Fasilitas') }}</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 fw-bold text-white mb-2">{{ __('Katalog Fasilitas & Lapangan Olahraga') }}</h1>
                    <p class="lead text-light text-opacity-75 mb-0" style="max-width: 650px;">
                        {{ __('Daftar lengkap lapangan olahraga di GOR Purnakrida Kerobokan. Dilengkapi spesifikasi teknis, daya tampung, dan tarif sewa resmi.') }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    {{-- Facilities Catalog Section --}}
    <section class="py-5" x-data="{ filter: 'all' }">
        <div class="container py-2">
            {{-- Filter Bar --}}
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small fw-semibold text-uppercase me-1">{{ __('Filter Kategori:') }}</span>
                    <button type="button" class="btn btn-sm rounded-pill px-3"
                            :class="filter === 'all' ? 'btn-primary' : 'btn-outline-secondary'"
                            @click="filter = 'all'">
                        {{ __('Semua') }} ({{ $fasilitas->count() }})
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3"
                            :class="filter === 'indoor' ? 'btn-primary' : 'btn-outline-secondary'"
                            @click="filter = 'indoor'">
                        <i class="bi bi-building me-1"></i> {{ __('Indoor') }}
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3"
                            :class="filter === 'outdoor' ? 'btn-primary' : 'btn-outline-secondary'"
                            @click="filter = 'outdoor'">
                        <i class="bi bi-sun me-1"></i> {{ __('Outdoor') }}
                    </button>
                </div>

                <div class="text-secondary small">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    {{ __('Tarif tertera dihitung per 1 sesi sewa') }}
                </div>
            </div>

            {{-- Grid Cards --}}
            <div class="row g-4">
                @forelse ($fasilitas as $f)
                    <div class="col-sm-6 col-lg-4" x-show="filter === 'all' || filter === '{{ $f->jenis }}'" x-transition>
                        <div class="card h-100 facility-card rounded-4 border-0 shadow-sm overflow-hidden bg-white">
                            <div class="facility-img-wrapper position-relative">
                                <img src="{{ $f->foto_url }}" class="facility-img" alt="{{ $f->nama }}" loading="lazy">

                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge rounded-pill bg-dark bg-opacity-75 text-white fw-medium px-3 py-1.5 shadow-sm backdrop-blur">
                                        <i class="bi {{ $f->jenis === 'indoor' ? 'bi-building text-info' : 'bi-sun text-warning' }} me-1"></i>
                                        {{ ucfirst($f->jenis) }}
                                    </span>
                                </div>

                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge rounded-pill bg-white text-dark fw-medium px-2.5 py-1.5 shadow-sm">
                                        <i class="bi bi-people-fill me-1 text-primary"></i>{{ $f->kapasitas }} Orang
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-4 d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark mb-1">
                                    <a href="{{ route('fasilitas.detail', $f) }}" class="text-decoration-none text-dark hover-primary" wire:navigate>
                                        {{ $f->nama }}
                                    </a>
                                </h5>

                                <div class="d-flex align-items-center gap-2 text-secondary small mb-3">
                                    <span><i class="bi bi-geo-alt text-primary"></i> Gelanggang {{ ucfirst($f->jenis) }}</span>
                                    <span>•</span>
                                    <span>Maks {{ $f->kapasitas }} orang</span>
                                </div>

                                <p class="card-text text-secondary small flex-grow-1 mb-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.5;">
                                    {{ $f->deskripsi }}
                                </p>

                                <div class="pt-3 border-top d-flex justify-content-between align-items-center mt-auto">
                                    <div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">Tarif Sewa</div>
                                        <div class="fw-bold text-primary fs-5 lh-1">
                                            Rp {{ number_format($f->tarif_per_sesi, 0, ',', '.') }}
                                            <span class="text-secondary fw-normal fs-6" style="font-size: 0.8rem;">/sesi</span>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-1.5">
                                        <a href="{{ route('fasilitas.detail', $f) }}" class="btn btn-light rounded-pill px-3 py-2 btn-sm fw-medium" wire:navigate>
                                            {{ __('Detail') }}
                                        </a>
                                        @auth
                                            <a href="{{ route('jadwal.index') }}" class="btn btn-primary rounded-pill px-3 py-2 btn-sm fw-semibold shadow-sm" wire:navigate>
                                                {{ __('Sewa') }}
                                            </a>
                                        @else
                                            <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-3 py-2 btn-sm fw-semibold shadow-sm" wire:navigate>
                                                {{ __('Sewa') }}
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border rounded-4 text-center py-5 shadow-sm">
                            <i class="bi bi-inbox fs-1 text-secondary mb-2 d-block"></i>
                            <h5 class="fw-bold">{{ __('Belum ada fasilitas yang aktif.') }}</h5>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Callout Box --}}
            <div class="mt-5 p-4 rounded-4 bg-white border shadow-sm">
                <div class="row align-items-center g-3">
                    <div class="col-md-8">
                        <h5 class="fw-bold text-dark mb-1">Ingin Menggunakan Lapangan untuk Acara atau Turnamen?</h5>
                        <p class="text-secondary small mb-0">
                            Untuk kebutuhan turnamen resmi antarklub, pekan olahraga, atau reservasi banyak slot berturut-turut, Anda dapat berkoordinasi terlebih dahulu dengan sekretariat pengelola GOR.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('kontak') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 small fw-semibold" wire:navigate>
                            <i class="bi bi-telephone-outbound me-1"></i> {{ __('Hubungi Pengelola') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
