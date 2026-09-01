<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan profil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $no_hp = '';

    /**
     * Mount component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->no_hp = Auth::user()->no_hp ?? '';
    }

    /**
     * Update profile information for currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated');

        session()->flash('status', __('Profil berhasil diperbarui.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Profil')" :subheading="__('Perbarui nama, email, dan nomor HP Anda')">
        <form wire:submit="updateProfileInformation" class="d-grid gap-3">
            <div>
                <label for="name" class="form-label">{{ __('Nama lengkap') }}</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                       wire:model="name" required autofocus autocomplete="name">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label for="email" class="form-label">{{ __('Alamat email') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                       wire:model="email" required autocomplete="email">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div>
                <label for="no_hp" class="form-label">{{ __('Nomor HP (WhatsApp)') }}</label>
                <input id="no_hp" type="tel" class="form-control @error('no_hp') is-invalid @enderror"
                       wire:model="no_hp" autocomplete="tel" placeholder="0812xxxxxxx">
                @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary" data-test="update-profile-button">
                    {{ __('Perbarui') }}
                </button>

                @if (session('status') === __('Profil berhasil diperbarui.'))
                    <span class="text-success small">{{ __('Tersimpan.') }}</span>
                @endif
            </div>
        </form>
    </x-pages::settings.layout>

    <livewire:pages::settings.delete-user-form />
</section>