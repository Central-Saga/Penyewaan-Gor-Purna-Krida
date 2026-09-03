<x-layouts::auth :title="__('Lupa kata sandi')">
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning-emphasis rounded-3 p-2 mb-2">
            <i class="bi bi-key-fill fs-4"></i>
        </div>
        <h2 class="h3 fw-bold text-dark mb-1">{{ __('Lupa Kata Sandi?') }}</h2>
        <p class="text-secondary small mb-0">{{ __('Masukkan alamat email yang terdaftar untuk menerima tautan atur ulang kata sandi.') }}</p>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label for="email" class="form-label small fw-semibold text-dark">{{ __('Alamat Email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                <input id="email" name="email" type="email" class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus autocomplete="email"
                       placeholder="nama@email.com">
            </div>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm w-100" data-test="email-password-reset-link-button">
            <i class="bi bi-send me-1"></i>
            {{ __('Kirim Tautan Atur Ulang') }}
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-secondary small">{{ __('Ingat kata sandi Anda?') }}</span>
        <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none ms-1 small" wire:navigate>
            {{ __('Kembali ke Masuk') }}
        </a>
    </div>
</x-layouts::auth>