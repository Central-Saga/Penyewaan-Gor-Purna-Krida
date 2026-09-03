<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $title ?? null])
    </head>
    <body class="bg-body-tertiary">
        <div class="app-wrapper" x-data="{ sidebarOpen: false }">
            {{-- Mobile Backdrop --}}
            <div class="sidebar-backdrop d-lg-none"
                 x-show="sidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 style="display: none;"></div>

            {{-- Sidebar Modern (Dark Slate) --}}
            <aside class="app-sidebar" :class="{ 'show': sidebarOpen }">
                <a href="{{ route('dashboard') }}" class="app-sidebar-brand" wire:navigate>
                    <div class="rounded-3 bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                        <i class="bi bi-trophy-fill fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-6 tracking-tight text-white lh-1">{{ config('app.name') }}</div>
                        <small class="text-secondary" style="font-size: 0.7rem;">DISDIKPORA Badung</small>
                    </div>
                </a>

                <div class="app-sidebar-body">
                    <div class="app-sidebar-section">{{ __('Menu Utama') }}</div>

                    <a class="app-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}" wire:navigate>
                        <i class="bi bi-speedometer2"></i>
                        <span>{{ __('Dashboard') }}</span>
                    </a>

                    @role('pengguna')
                        <div class="app-sidebar-section mt-3">{{ __('Layanan Sewa') }}</div>
                        <a class="app-nav-link {{ request()->routeIs('jadwal.*') ? 'active' : '' }}"
                           href="{{ route('jadwal.index') }}" wire:navigate>
                            <i class="bi bi-calendar2-check"></i>
                            <span>{{ __('Sewa Fasilitas') }}</span>
                        </a>
                        <a class="app-nav-link {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}"
                           href="{{ route('peminjaman.index') }}" wire:navigate>
                            <i class="bi bi-receipt"></i>
                            <span>{{ __('Peminjaman Saya') }}</span>
                        </a>
                    @endrole

                    @hasrole('pengelola')
                        <div class="app-sidebar-section mt-3">{{ __('Operasional') }}</div>
                        <a class="app-nav-link {{ request()->routeIs('verifikasi.*') ? 'active' : '' }}"
                           href="{{ route('verifikasi.index') }}" wire:navigate>
                            <i class="bi bi-shield-check"></i>
                            <span>{{ __('Verifikasi Pembayaran') }}</span>
                        </a>
                        <a class="app-nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}"
                           href="{{ route('transaksi.index') }}" wire:navigate>
                            <i class="bi bi-credit-card-2-front"></i>
                            <span>{{ __('Data Transaksi') }}</span>
                        </a>
                        <div class="app-sidebar-section mt-3">{{ __('Kelola Gelanggang') }}</div>
                        <a class="app-nav-link {{ request()->routeIs('panel.fasilitas.*') ? 'active' : '' }}"
                           href="{{ route('panel.fasilitas.index') }}" wire:navigate>
                            <i class="bi bi-grid-3x3-gap"></i>
                            <span>{{ __('Fasilitas Lapangan') }}</span>
                        </a>
                        <a class="app-nav-link {{ request()->routeIs('panel.slot-sesi.*') || request()->routeIs('panel.blokir-slot.*') ? 'active' : '' }}"
                           href="{{ route('panel.slot-sesi.index') }}" wire:navigate>
                            <i class="bi bi-clock-history"></i>
                            <span>{{ __('Slot & Blokir Jadwal') }}</span>
                        </a>
                    @endhasrole

                    @role('admin')
                        <div class="app-sidebar-section mt-3">{{ __('Administrasi') }}</div>
                        <a class="app-nav-link {{ request()->routeIs('panel.pengguna.*') ? 'active' : '' }}"
                           href="{{ route('panel.pengguna.index') }}" wire:navigate>
                            <i class="bi bi-people"></i>
                            <span>{{ __('Kelola Pengguna') }}</span>
                        </a>
                        <a class="app-nav-link {{ request()->routeIs('transaksi.*') ? 'active' : '' }}"
                           href="{{ route('transaksi.index') }}" wire:navigate>
                            <i class="bi bi-credit-card-2-front"></i>
                            <span>{{ __('Data Transaksi') }}</span>
                        </a>
                        <a class="app-nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}"
                           href="{{ route('laporan.index') }}" wire:navigate>
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>{{ __('Laporan & Rekap') }}</span>
                        </a>
                    @endrole

                    <div class="app-sidebar-section mt-3">{{ __('Publik') }}</div>
                    <a class="app-nav-link" href="{{ route('home') }}" wire:navigate target="_blank">
                        <i class="bi bi-box-arrow-up-right"></i>
                        <span>{{ __('Lihat Website') }}</span>
                    </a>
                </div>

                <div class="app-sidebar-footer">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <div class="rounded-circle bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; min-width: 36px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="text-truncate">
                                <div class="text-white small fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                                <div class="text-secondary" style="font-size: 0.72rem;">{{ ucfirst(auth()->user()->roles->first()?->name ?? 'Pengguna') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Main Shell --}}
            <div class="app-main">
                {{-- Topbar --}}
                <header class="app-topbar">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-secondary d-lg-none border-0 p-1" type="button" @click="sidebarOpen = !sidebarOpen">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                        <h6 class="fw-bold mb-0 text-dark d-none d-sm-block">{{ $title ?? __('Panel Kontrol') }}</h6>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button class="btn btn-light rounded-pill border px-3 py-1.5 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="rounded-circle bg-primary text-white small d-flex align-items-center justify-content-center fw-bold" style="width: 24px; height: 24px;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="small fw-semibold d-none d-md-inline">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down text-secondary" style="font-size: 0.75rem;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                                <li class="px-3 py-2 border-bottom">
                                    <div class="fw-bold small">{{ auth()->user()->name }}</div>
                                    <div class="text-secondary small">{{ auth()->user()->email }}</div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="{{ route('profile.edit') }}" wire:navigate>
                                        <i class="bi bi-gear text-secondary"></i>
                                        <span>{{ __('Pengaturan Akun') }}</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 small text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i>
                                            <span>{{ __('Keluar') }}</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </header>

                {{-- Page Content Slot --}}
                <main class="app-content">
                    {{ $slot }}
                </main>

                {{-- App Footer --}}
                <footer class="app-footer text-center">
                    &copy; {{ date('Y') }} {{ config('app.name') }} — Dinas Pendidikan, Kepemudaan dan Olahraga Kabupaten Badung.
                </footer>
            </div>
        </div>

        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            @foreach (session('laravel_flash_message', []) as $message)
                <div class="toast align-items-center text-bg-success border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">{{ $message }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                    </div>
                </div>
            @endforeach
        </div>

        @stack('scripts')
    </body>
</html>
