<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    use PasswordValidationRules;

    public string $password = '';
    public bool $showing = false;

    /**
     * Delete currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="mt-5">
    <h2 class="h5 fw-bold">{{ __('Hapus akun') }}</h2>
    <p class="text-secondary">{{ __('Hapus akun Anda beserta seluruh data terkait') }}</p>

    <button type="button" class="btn btn-outline-danger" data-test="delete-user-button"
            wire:click="$set('showing', true)">
        {{ __('Hapus akun') }}
    </button>

    @if ($showing)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form wire:submit="deleteUser">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Anda yakin ingin menghapus akun?') }}</h5>
                            <button type="button" class="btn-close" wire:click="$set('showing', false)" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-secondary small">
                                {{ __('Setelah akun dihapus, seluruh data akan dihapus permanen. Masukkan kata sandi untuk konfirmasi.') }}
                            </p>
                            <div>
                                <label for="delete-password" class="form-label">{{ __('Kata sandi') }}</label>
                                <input id="delete-password" type="password" class="form-control @error('password') is-invalid @enderror"
                                       wire:model="password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$set('showing', false')">
                                {{ __('Batal') }}
                            </button>
                            <button type="submit" class="btn btn-danger" data-test="confirm-delete-user-button">
                                {{ __('Hapus akun') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</section>