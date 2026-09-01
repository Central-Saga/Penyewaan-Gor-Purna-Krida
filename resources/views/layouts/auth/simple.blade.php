<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="bg-body-tertiary d-flex align-items-center justify-content-center min-vh-100">
        <div class="container" style="max-width: 420px;">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none text-body" wire:navigate>
                    <x-app-logo />
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>