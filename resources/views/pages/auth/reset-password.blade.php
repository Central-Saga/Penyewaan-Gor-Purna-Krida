<x-layouts::auth :title="__('Atur ulang kata sandi')">
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3 p-2 mb-2">
            <i class="bi bi-shield-lock-fill fs-4"></i>
        </div>
        <h2 class="h3 fw-bold text-dark mb-1">{{ __('Atur Ulang Kata Sandi') }}</h2>
        <p class="text-secondary small mb-0">{{ __('Tentukan kata sandi baru untuk akun Anda.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="d-grid gap-3">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div>
            <label for="email" class="form-label small fw-semibold text-dark">{{ __('Alamat Email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                <input id="email" name="email" type="email" class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                       value="{{ request('email') }}" required autocomplete="email">
            </div>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="password" class="form-label small fw-semibold text-dark">{{ __('Kata Sandi Baru') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
                <input id="password" name="password" type="password" class="form-control border-start-0 ps-1 @error('password') is-invalid @enderror"
                       required autocomplete="new-password" placeholder="••••••••">
            </div>
            @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label small fw-semibold text-dark">{{ __('Konfirmasi Kata Sandi Baru') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-shield-lock"></i></span>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control border-start-0 ps-1"
                       required autocomplete="new-password" placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm w-100 mt-2" data-test="reset-password-button">
            <i class="bi bi-check2-circle me-1"></i>
            {{ __('Simpan Kata Sandi Baru') }}
        </button>
    </form>
</x-layouts::auth>