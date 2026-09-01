<x-layouts::auth :title="__('Daftar')">
    <x-auth-header :title="__('Buat akun baru')" :description="__('Isi data di bawah untuk membuat akun')" />

    <form method="POST" action="{{ route('register.store') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label for="name" class="form-label">{{ __('Nama lengkap') }}</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="email" class="form-label">{{ __('Alamat email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autocomplete="email" placeholder="email@example.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="no_hp" class="form-label">{{ __('Nomor HP (WhatsApp)') }}</label>
            <input id="no_hp" name="no_hp" type="tel" class="form-control @error('no_hp') is-invalid @enderror"
                   value="{{ old('no_hp') }}" autocomplete="tel" placeholder="0812xxxxxxx">
            @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="password" class="form-label">{{ __('Kata sandi') }}</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">{{ __('Konfirmasi kata sandi') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                   required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100" data-test="register-user-button">
            {{ __('Daftar') }}
        </button>
    </form>

    <p class="text-center text-secondary mt-3 mb-0">
        <span>{{ __('Sudah punya akun?') }}</span>
        <a href="{{ route('login') }}" wire:navigate>{{ __('Masuk') }}</a>
    </p>
</x-layouts::auth>