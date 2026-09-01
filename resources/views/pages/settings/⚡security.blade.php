<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan keamanan')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        session()->flash('status', __('Kata sandi berhasil diperbarui.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Perbarui kata sandi')" :subheading="__('Pastikan akun menggunakan kata sandi panjang dan acak agar tetap aman')">
        <form wire:submit="updatePassword" class="d-grid gap-3">
            <div>
                <label for="current_password" class="form-label">{{ __('Kata sandi saat ini') }}</label>
                <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror"
                       wire:model="current_password" required autocomplete="current-password">
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label for="password" class="form-label">{{ __('Kata sandi baru') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                       wire:model="password" required autocomplete="new-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="form-label">{{ __('Konfirmasi kata sandi baru') }}</label>
                <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                       wire:model="password_confirmation" required autocomplete="new-password">
                @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary" data-test="update-password-button">
                    {{ __('Perbarui') }}
                </button>

                @if (session('status') === __('Kata sandi berhasil diperbarui.'))
                    <span class="text-success small">{{ __('Tersimpan.') }}</span>
                @endif
            </div>
        </form>
    </x-pages::settings.layout>
</section>