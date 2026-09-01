<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

new #[Title('Kelola Pengguna')] class extends Component
{
    use WithPagination;

    public string $cari = '';

    public function updatingCari(): void
    {
        $this->resetPage();
    }

    public function setRole(int $userId, string $role): void
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403);
        }

        if (! in_array($role, ['pengguna', 'pengelola', 'admin'], true)) {
            return;
        }

        $target = User::findOrFail($userId);
        $aktif = auth()->user();

        // F3.4: tidak bisa mengubah role diri sendiri.
        if ($target->id === $aktif->id) {
            $this->addError('role', __('Anda tidak dapat mengubah role akun sendiri.'));

            return;
        }

        // Proteksi admin terakhir.
        if ($target->isAdmin() && $role !== 'admin' && User::role('admin')->count() <= 1) {
            $this->addError('role', __('Minimal harus ada satu admin.'));

            return;
        }

        $target->syncRoles([$role]);

        session()->flash('status', __('Role pengguna berhasil diperbarui.'));
    }

    public function hapus(int $userId): void
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403);
        }

        $target = User::findOrFail($userId);
        $aktif = auth()->user();

        // F3.4: tidak bisa hapus diri sendiri.
        if ($target->id === $aktif->id) {
            $this->addError('hapus', __('Anda tidak dapat menghapus akun sendiri.'));

            return;
        }

        // Tidak bisa hapus admin terakhir.
        if ($target->isAdmin() && User::role('admin')->count() <= 1) {
            $this->addError('hapus', __('Tidak dapat menghapus admin terakhir.'));

            return;
        }

        $target->delete();

        session()->flash('status', __('Pengguna berhasil dihapus.'));
    }

    public function render()
    {
        return view('livewire::panel.pengguna.index', [
            'daftarPengguna' => User::query()
                ->when($this->cari, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->cari}%")
                        ->orWhere('email', 'like', "%{$this->cari}%");
                }))
                ->with('roles')
                ->orderBy('name')
                ->paginate(15),
        ])->layout('layouts.app');
    }
}; ?>

<div>
    <h1 class="h4 fw-bold mb-4">{{ __('Kelola Pengguna') }}</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @error('role')<div class="alert alert-danger">{{ $message }}</div>@enderror
    @error('hapus')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <input type="text" class="form-control mb-3" style="max-width: 320px;"
           placeholder="{{ __('Cari nama atau email...') }}" wire:model.live.debounce.300ms="cari">

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">{{ __('Nama') }}</th>
                    <th scope="col">{{ __('Email') }}</th>
                    <th scope="col">{{ __('No. HP') }}</th>
                    <th scope="col">{{ __('Role') }}</th>
                    <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPengguna as $pengguna)
                    <tr>
                        <td class="fw-semibold">{{ $pengguna->name }}</td>
                        <td>{{ $pengguna->email }}</td>
                        <td>{{ $pengguna->no_hp ?? '—' }}</td>
                        <td>
                            <select class="form-select form-select-sm" style="min-width: 130px;"
                                    wire:change="setRole({{ $pengguna->id }}, $event.target.value)"
                                    wire:key="role-{{ $pengguna->id }}">
                                @foreach (['pengguna', 'pengelola', 'admin'] as $role)
                                    <option value="{{ $role }}"
                                            @selected($pengguna->hasRole($role))>{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-end">
                            <button wire:click="hapus({{ $pengguna->id }})"
                                    wire:confirm="{{ __('Hapus pengguna ini?') }}"
                                    @disabled($pengguna->id === auth()->id())
                                    class="btn btn-sm btn-outline-danger">{{ __('Hapus') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">{{ __('Belum ada pengguna.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $daftarPengguna->links() }}
</div>