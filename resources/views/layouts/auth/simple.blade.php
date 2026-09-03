<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-body-tertiary min-vh-100 d-flex flex-column">
        <div class="container-fluid p-0 flex-grow-1 d-flex">
            <div class="row g-0 w-100 flex-grow-1 align-items-stretch">
                {{-- Form Column --}}
                <div class="col-lg-6 col-xl-5 d-flex flex-column justify-content-between p-4 p-sm-5 bg-white position-relative">
                    {{-- Header Logo --}}
                    <div class="mb-4">
                        <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none text-dark fw-bold fs-5 tracking-tight" wire:navigate>
                            <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                                <i class="bi bi-trophy-fill fs-5"></i>
                            </div>
                            <span>{{ config('app.name') }}</span>
                        </a>
                    </div>

                    {{-- Main Auth Content Slot --}}
                    <div class="my-auto mx-auto w-100 auth-form-anim py-2" style="max-width: 440px;">
                        {{ $slot }}
                    </div>

                    {{-- Footer Note --}}
                    <div class="mt-4 pt-3 border-top text-secondary small d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                        <span>&copy; {{ date('Y') }} DISDIKPORA Kab. Badung</span>
                        <a href="{{ route('home') }}" class="text-decoration-none text-secondary d-inline-flex align-items-center gap-1" wire:navigate>
                            <i class="bi bi-arrow-left"></i>
                            <span>{{ __('Kembali ke Beranda') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Visual Column (Desktop) --}}
                <div class="col-lg-6 col-xl-7 d-none d-lg-flex position-relative flex-column justify-content-between p-5 text-white overflow-hidden"
                     style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.88) 0%, rgba(15, 23, 42, 0.95) 100%),
                                url('https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;">

                    {{-- Top Status Pill --}}
                    <div class="d-flex justify-content-end">
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 small backdrop-blur">
                            <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-shield-check me-1"></i>RESMI</span>
                            <span>DISDIKPORA Kabupaten Badung</span>
                        </div>
                    </div>

                    {{-- Center Showcase Card --}}
                    <div class="my-auto mx-auto w-100 auth-visual-card" style="max-width: 520px;">
                        <div class="rounded-4 p-4 p-xl-5 bg-white bg-opacity-10 border border-white border-opacity-20 backdrop-blur shadow-lg">
                            <div class="d-flex gap-1 text-warning mb-3">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <h3 class="fw-bold text-white mb-3 lh-base">
                                "Pusat Gelanggang Olahraga Resmi Terlengkap di Kabupaten Badung."
                            </h3>
                            <p class="text-light text-opacity-75 small mb-4 lh-base">
                                Nikmati kemudahan reservasi lapangan badminton indoor, bola basket, bola voli outdoor, dan tenis meja secara terpadu tanpa perantara.
                            </p>

                            <div class="d-flex align-items-center gap-3 pt-3 border-top border-white border-opacity-20">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 44px; height: 44px;">
                                    GP
                                </div>
                                <div>
                                    <div class="fw-semibold text-white">Kompleks GOR Purnakrida</div>
                                    <div class="text-light text-opacity-75 small">Kerobokan Kaja, Kuta Utara, Badung</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Highlights Strip --}}
                    <div class="d-flex flex-wrap gap-4 text-light text-opacity-75 small">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> 5+ Lapangan Aktif
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-info"></i> Jadwal Realtime 24/7
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-tag-fill text-warning"></i> Tarif Resmi Terstandar
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>