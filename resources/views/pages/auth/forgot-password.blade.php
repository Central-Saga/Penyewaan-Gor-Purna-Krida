<x-layouts::auth :title="__('Lupa kata sandi')">
    <x-auth-header :title="__('Lupa kata sandi')" :description="__('Masukkan email untuk menerima tautan atur ulang kata sandi')" />

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label for="email" class="form-label">{{ __('Alamat email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus autocomplete="email"
                   placeholder="email@example.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary w-100" data-test="email-password-reset-link-button">
            {{ __('Kirim tautan atur ulang kata sandi') }}
        </button>
    </form>

    <p class="text-center text-secondary mt-3 mb-0">
        <span>{{ __('Kembali ke') }}</span>
        <a href="{{ route('login') }}" wire:navigate>{{ __('halaman masuk') }}</a>
    </p>
</x-layouts::auth>