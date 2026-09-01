<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-body-tertiary d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}" wire:navigate>
                    <x-app-logo />
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
                        aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigasi">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="sidebarMenu">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                               href="{{ route('dashboard') }}" wire:navigate>{{ __('Dashboard') }}</a>
                        </li>
                        @role('pengguna')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Sewa Fasilitas') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Peminjaman Saya') }}</a>
                            </li>
                        @endrole
                        @hasrole('pengelola')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Fasilitas') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Jadwal') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Verifikasi') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Transaksi') }}</a>
                            </li>
                        @endhasrole
                        @role('admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Pengguna') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}" wire:navigate>{{ __('Laporan') }}</a>
                            </li>
                        @endrole
                    </ul>

                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false" data-test="sidebar-menu-button">
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">{{ auth()->user()->email }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}" wire:navigate>
                                    {{ __('Pengaturan') }}
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item" data-test="logout-button">
                                        {{ __('Keluar') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <main class="container py-4 flex-grow-1">
            {{ $slot }}
        </main>

        <footer class="border-top py-3 bg-white">
            <div class="container text-center text-secondary small">
                &copy; {{ date('Y') }} {{ config('app.name') }} — DISDIKPORA Badung
            </div>
        </footer>

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