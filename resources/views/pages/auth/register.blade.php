<x-layouts::auth :title="__('Daftar Akun')">
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3 p-2 mb-2">
            <i class="bi bi-person-plus-fill fs-4"></i>
        </div>
        <h2 class="h3 fw-bold text-dark mb-1">{{ __('Buat Akun Baru') }}</h2>
        <p class="text-secondary small mb-0">{{ __('Daftarkan akun untuk menyewa fasilitas dan memantau jadwal secara daring.') }}</p>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label for="name" class="form-label small fw-semibold text-dark">{{ __('Nama Lengkap') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-person"></i></span>
                <input id="name" name="name" type="text" class="form-control border-start-0 ps-1 @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autofocus autocomplete="name"
                       placeholder="Contoh: I Wayan Sudarma">
            </div>
            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="email" class="form-label small fw-semibold text-dark">{{ __('Alamat Email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                <input id="email" name="email" type="email" class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com">
            </div>
            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="no_hp" class="form-label small fw-semibold text-dark">{{ __('Nomor HP (WhatsApp)') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-whatsapp"></i></span>
                <input id="no_hp" name="no_hp" type="tel" class="form-control border-start-0 ps-1 @error('no_hp') is-invalid @enderror"
                       value="{{ old('no_hp') }}" autocomplete="tel" placeholder="08123456789">
            </div>
            <div class="form-text text-muted" style="font-size: 0.75rem;">Digunakan untuk konfirmasi jadwal & verifikasi pembayaran.</div>
            @error('no_hp')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>

        <div class="row g-2">
            <div class="col-sm-6">
                <label for="password" class="form-label small fw-semibold text-dark">{{ __('Kata Sandi') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
                    <input id="password" name="password" type="password" class="form-control border-start-0 ps-1 @error('password') is-invalid @enderror"
                           required autocomplete="new-password" placeholder="••••••••">
                </div>
                @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="col-sm-6">
                <label for="password_confirmation" class="form-label small fw-semibold text-dark">{{ __('Konfirmasi Sandi') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-shield-lock"></i></span>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control border-start-0 ps-1"
                           required autocomplete="new-password" placeholder="••••••••">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill py-2.5 fw-semibold shadow-sm w-100 mt-2" data-test="register-user-button">
            <i class="bi bi-person-plus me-1"></i>
            {{ __('Daftar Akun Sekarang') }}
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
        <span class="text-secondary small">{{ __('Sudah memiliki akun?') }}</span>
        <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-none ms-1 small" wire:navigate>
            {{ __('Masuk ke Akun') }}
        </a>
    </div>
</x-layouts::auth>