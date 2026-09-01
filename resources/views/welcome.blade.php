<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-white">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    {{ config('app.name') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#homeNav"
                        aria-controls="homeNav" aria-expanded="false" aria-label="Toggle navigasi">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="homeNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                        <li class="nav-item">
                            <a class="nav-link" href="#fasilitas">{{ __('Fasilitas') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#kontak">{{ __('Kontak') }}</a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm" wire:navigate>
                                    {{ __('Dashboard') }}
                                </a>
                            </li>
                        @endauth
                        @guest
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm" wire:navigate>
                                    {{ __('Masuk') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm" wire:navigate>
                                    {{ __('Daftar') }}
                                </a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <header class="bg-dark text-white py-5">
            <div class="container py-4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <h1 class="display-5 fw-bold">{{ __('Sewa Fasilitas GOR Purnakrida') }}</h1>
                        <p class="lead text-white-50">
                            {{ __('Badan lapangan indoor dan outdoor milik DISDIKPORA Badung. Pilih fasilitas, pilih slot jadwal, selesaikan pemesanan secara daring.') }}
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-2" wire:navigate>
                            {{ __('Sewa Sekarang') }}
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <section id="fasilitas" class="py-5">
            <div class="container">
                <h2 class="h3 fw-bold mb-4">{{ __('Fasilitas Kami') }}</h2>
                <div class="row g-4">
                    @forelse ($fasilitas as $f)
                        <div class="col-sm-6 col-lg-4">
                            <x-fasilitas-card :fasilitas="$f" :action="route('login')" />
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light border">{{ __('Belum ada fasilitas yang tersedia.') }}</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="kontak" class="py-5 bg-body-tertiary border-top">
            <div class="container">
                <h2 class="h3 fw-bold mb-4">{{ __('Kontak') }}</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-secondary small">{{ __('Alamat') }}</h6>
                        <p class="mb-0">GOR Purnakrida, DISDIKPORA Kabupaten Badung</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-uppercase text-secondary small">{{ __('Jam Layanan') }}</h6>
                        <p class="mb-0">{{ __('Senin–Sabtu, 08.00–16.00 WITA') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <footer class="border-top py-4 bg-white">
            <div class="container text-center text-secondary small">
                &copy; {{ date('Y') }} {{ config('app.name') }} — DISDIKPORA Badung
            </div>
        </footer>
    </body>
</html>