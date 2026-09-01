<x-layouts::auth :title="__('Atur ulang kata sandi')">
    <x-auth-header :title="__('Atur ulang kata sandi')" :description="__('Masukkan kata sandi baru Anda di bawah ini')" />

    <form method="POST" action="{{ route('password.update') }}" class="d-grid gap-3">
        @csrf

        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div>
            <label for="email" class="form-label">{{ __('Alamat email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ request('email') }}" required autocomplete="email">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="password" class="form-label">{{ __('Kata sandi baru') }}</label>
            <input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label">{{ __('Konfirmasi kata sandi baru') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                   required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary w-100" data-test="reset-password-button">
            {{ __('Atur ulang kata sandi') }}
        </button>
    </form>
</x-layouts::auth>