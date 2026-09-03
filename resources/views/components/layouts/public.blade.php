<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title ?? null])
    </head>
    <body class="bg-body-tertiary d-flex flex-column min-vh-100">
        {{-- Modern Public Navbar --}}
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-3 border-bottom border-secondary border-opacity-25 shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold tracking-tight" href="{{ route('home') }}" wire:navigate>
                    <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                        <i class="bi bi-trophy-fill fs-5"></i>
                    </div>
                    <span>{{ config('app.name') }}</span>
                </a>

                <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#homeNav"
                        aria-controls="homeNav" aria-expanded="false" aria-label="Toggle navigasi">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="homeNav">
                    <ul class="navbar-nav mx-auto gap-lg-1">
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium {{ request()->routeIs('home') ? 'active text-primary fw-semibold' : '' }}"
                               href="{{ route('home') }}" wire:navigate>
                                {{ __('Beranda') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium {{ request()->routeIs('fasilitas.*') ? 'active text-primary fw-semibold' : '' }}"
                               href="{{ route('fasilitas.public') }}" wire:navigate>
                                {{ __('Fasilitas') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium {{ request()->routeIs('panduan') ? 'active text-primary fw-semibold' : '' }}"
                               href="{{ route('panduan') }}" wire:navigate>
                                {{ __('Alur & Panduan') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium {{ request()->routeIs('tentang') ? 'active text-primary fw-semibold' : '' }}"
                               href="{{ route('tentang') }}" wire:navigate>
                                {{ __('Tentang Kami') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-medium {{ request()->routeIs('kontak') ? 'active text-primary fw-semibold' : '' }}"
                               href="{{ route('kontak') }}" wire:navigate>
                                {{ __('Kontak & Lokasi') }}
                            </a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light rounded-pill px-4 btn-sm fw-medium d-inline-flex align-items-center gap-1.5" wire:navigate>
                                <i class="bi bi-speedometer2"></i>
                                <span>{{ __('Dashboard') }}</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-3 py-1.5 btn-sm fw-medium" wire:navigate>
                                {{ __('Masuk') }}
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-3 py-1.5 btn-sm fw-medium shadow-sm" wire:navigate>
                                {{ __('Daftar Akun') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main Page Content --}}
        <main class="flex-grow-1">
            {{ $slot }}
        </main>

        {{-- Modern Footer --}}
        <footer class="bg-dark text-white pt-5 pb-4 border-top border-secondary border-opacity-25 mt-auto">
            <div class="container">
                <div class="row g-4 mb-4">
                    <div class="col-lg-5">
                        <div class="d-flex align-items-center gap-2 fw-bold fs-5 mb-2">
                            <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-trophy-fill fs-6"></i>
                            </div>
                            <span>{{ config('app.name') }}</span>
                        </div>
                        <p class="text-secondary small mb-3" style="max-width: 380px;">
                            Sistem Informasi Pemesanan dan Penyewaan Lapangan Olahraga GOR Purnakrida Kerobokan, Dinas Pendidikan, Kepemudaan dan Olahraga (DISDIKPORA) Kabupaten Badung, Bali.
                        </p>
                        <div class="text-secondary small">
                            <i class="bi bi-geo-alt me-1 text-primary"></i> Jl. Raya Kerobokan, Kerobokan Kaja, Kuta Utara, Badung
                        </div>
                    </div>

                    <div class="col-6 col-lg-3">
                        <h6 class="text-uppercase fw-bold small text-white-50 mb-3">{{ __('Halaman') }}</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2 small mb-0">
                            <li><a href="{{ route('home') }}" class="text-secondary text-decoration-none hover-white" wire:navigate>{{ __('Beranda') }}</a></li>
                            <li><a href="{{ route('fasilitas.public') }}" class="text-secondary text-decoration-none hover-white" wire:navigate>{{ __('Daftar Fasilitas') }}</a></li>
                            <li><a href="{{ route('panduan') }}" class="text-secondary text-decoration-none hover-white" wire:navigate>{{ __('Panduan Pemesanan') }}</a></li>
                            <li><a href="{{ route('tentang') }}" class="text-secondary text-decoration-none hover-white" wire:navigate>{{ __('Tentang GOR') }}</a></li>
                            <li><a href="{{ route('kontak') }}" class="text-secondary text-decoration-none hover-white" wire:navigate>{{ __('Kontak & Lokasi') }}</a></li>
                        </ul>
                    </div>

                    <div class="col-6 col-lg-4">
                        <h6 class="text-uppercase fw-bold small text-white-50 mb-3">{{ __('Layanan Sewa') }}</h6>
                        <div class="d-flex flex-column gap-2 small mb-3">
                            <span class="text-secondary">Jam Pelayanan: Senin - Sabtu (08.00 - 16.00 WITA)</span>
                            <span class="text-secondary">Pusat Olahraga Resmi DISDIKPORA Kab. Badung</span>
                        </div>
                        <div>
                            @auth
                                <a href="{{ route('jadwal.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5" wire:navigate>
                                    <i class="bi bi-calendar2-check me-1"></i> {{ __('Sewa Lapangan') }}
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-3 py-1.5" wire:navigate>
                                    <i class="bi bi-person-plus me-1"></i> {{ __('Daftar Sekarang') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-top border-secondary border-opacity-25 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-secondary">
                    <div>
                        &copy; {{ date('Y') }} {{ config('app.name') }}. DISDIKPORA Kabupaten Badung, Bali.
                    </div>
                    <div>
                        {{ __('Sistem Informasi Penyewaan Lapangan Daring') }}
                    </div>
                </div>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>