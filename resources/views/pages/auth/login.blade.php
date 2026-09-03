<x-layouts::auth :title="__('Masuk')">
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3 p-2 mb-2">
            <i class="bi bi-box-arrow-in-right fs-4"></i>
        </div>
        <h2 class="h3 fw-bold text-dark mb-1">{{ __('Selamat Datang Kembali') }}</h2>
        <p class="text-secondary small mb-0">{{ __('Masuk ke akun Anda untuk mengecek jadwal & mengelola reservasi.') }}</p>
    </div>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="d-grid gap-3">
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

        <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label small fw-semibold text-dark mb-0">{{ __('Kata Sandi') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary fw-medium" wire:navigate>
                        {{ __('Lupa kata sandi?') }}
                    </a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
                <input id="password" name="password" type="password" class="form-control border-start-0 ps-1 @error('password') is-invalid @enderror"
                       required autocomplete="current-password" placeholder="••••••••">
            </div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1">
                <label class="form-check-label small text-secondary" for="remember">{{ __('Ingat saya di perangkat ini') }}</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm w-100" data-test="login-button">
            <i class="bi bi-box-arrow-in-right me-1"></i>
            {{ __('Masuk ke Akun') }}
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-secondary small">{{ __('Belum memiliki akun?') }}</span>
        <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-none ms-1 small" wire:navigate>
            {{ __('Daftar Sekarang') }}
        </a>
    </div>
</x-layouts::auth>