<div class="d-flex align-items-start flex-column flex-md-row">
    <div class="me-md-4 mb-3 mb-md-0" style="min-width: 200px;">
        <nav class="nav flex-column nav-pills" aria-label="{{ __('Pengaturan') }}">
            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}"
               href="{{ route('profile.edit') }}" wire:navigate>{{ __('Profil') }}</a>
            <a class="nav-link {{ request()->routeIs('security.edit') ? 'active' : '' }}"
               href="{{ route('security.edit') }}" wire:navigate>{{ __('Keamanan') }}</a>
        </nav>
    </div>

    <div class="flex-grow-1">
        <h1 class="h5 fw-bold">{{ $heading ?? '' }}</h1>
        <p class="text-secondary">{{ $subheading ?? '' }}</p>

        <div class="mt-4" style="max-width: 640px;">
            {{ $slot }}
        </div>
    </div>
</div>