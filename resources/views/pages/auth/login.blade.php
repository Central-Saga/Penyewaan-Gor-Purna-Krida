<x-layouts::auth :title="__('Masuk')">
    <x-auth-header :title="__('Masuk ke akun Anda')" :description="__('Masukkan email dan kata sandi untuk masuk')" />

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login.store') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label for="email" class="form-label">{{ __('Alamat email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus autocomplete="email"
                   placeholder="email@example.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div>
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">{{ __('Kata sandi') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none" wire:navigate>
                        {{ __('Lupa kata sandi?') }}
                    </a>
                @endif
            </div>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="current-password" placeholder="{{ __('Kata sandi') }}">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-check">
            <input id="remember" name="remember" type="checkbox" class="form-check-input" value="1">
            <label class="form-check-label" for="remember">{{ __('Ingat saya') }}</label>
        </div>

        <button type="submit" class="btn btn-primary w-100" data-test="login-button">
            {{ __('Masuk') }}
        </button>
    </form>

    <p class="text-center text-secondary mt-3 mb-0">
        <span>{{ __('Belum punya akun?') }}</span>
        <a href="{{ route('register') }}" wire:navigate>{{ __('Daftar') }}</a>
    </p>
</x-layouts::auth>