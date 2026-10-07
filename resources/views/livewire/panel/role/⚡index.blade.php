<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

new #[Title('Role & Permission')] class extends Component
{
    /**
     * Permission yang wajib dimiliki role admin agar tidak terjadi lockout.
     */
    private const PERMISSION_WAJIB_ADMIN = 'kelola_pengguna';

    public function togglePermission(string $roleName, string $permissionName): void
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403);
        }

        $role = Role::findByName($roleName, 'web');
        $permission = Permission::findByName($permissionName, 'web');

        // Anti-lockout: role admin wajib tetap memiliki kelola_pengguna.
        if ($role->name === 'admin' && $permission->name === self::PERMISSION_WAJIB_ADMIN && $role->hasPermissionTo($permission)) {
            $this->addError('permission', __('Permission :permission wajib dimiliki role admin.', ['permission' => $permission->name]));

            return;
        }

        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
        } else {
            $role->givePermissionTo($permission);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        session()->flash('status', __('Permission :role berhasil diperbarui.', ['role' => ucfirst($role->name)]));
    }

    public function render()
    {
        if (! auth()->user()?->isAdmin()) {
            abort(403);
        }

        return $this->view([
            'daftarRole' => Role::query()->with('permissions')->orderBy('name')->get(),
            'daftarPermission' => Permission::query()->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}; ?>

<div>
    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">{{ __('Role & Permission') }}</h1>
        <p class="text-secondary mb-0">{{ __('Atur hak akses setiap role. Perubahan berlaku langsung untuk seluruh pengguna dengan role tersebut.') }}</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('permission')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <div class="alert alert-info border-0 rounded-4 small d-flex gap-2">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
            {{ __('Role admin wajib menyimpan permission "kelola_pengguna" agar sistem tidak terkunci. Role pengguna tidak memiliki permission khusus (hanya akses fitur penyewa).') }}
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">{{ __('Permission') }}</th>
                    @foreach ($daftarRole as $role)
                        <th scope="col" class="text-center text-capitalize">{{ $role->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPermission as $permission)
                    <tr wire:key="perm-{{ $permission->id }}">
                        <td>
                            <div class="fw-semibold text-dark font-monospace">{{ $permission->name }}</div>
                        </td>
                        @foreach ($daftarRole as $role)
                            @php
                                $punya = $role->permissions->contains('id', $permission->id);
                                $terkunci = $role->name === 'admin' && $permission->name === 'kelola_pengguna';
                            @endphp
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input" type="checkbox"
                                           role="switch"
                                           wire:key="toggle-{{ $role->id }}-{{ $permission->id }}"
                                           wire:change="togglePermission('{{ $role->name }}', '{{ $permission->name }}')"
                                           @checked($punya)
                                           @disabled($terkunci)
                                           title="{{ $terkunci ? __('Permission wajib, tidak dapat dicabut.') : '' }}">
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $daftarRole->count() + 1 }}" class="text-center text-secondary py-4">{{ __('Belum ada permission terdaftar.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
