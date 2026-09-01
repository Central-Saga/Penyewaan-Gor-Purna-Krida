<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Peminjaman;
use App\Services\BookingService;

new #[Title('Peminjaman')] class extends Component
{
    use WithPagination;

    public string $status = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function batalkan(BookingService $bookingService, int $id): void
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = auth()->user();

        // Pengguna hanya boleh batal miliknya sendiri.
        if (! $user->hasAnyRole(['admin', 'pengelola']) && $peminjaman->user_id !== $user->id) {
            abort(403);
        }

        if (! in_array($peminjaman->status, [Peminjaman::MENUNGGU_PEMBAYARAN, Peminjaman::MENUNGGU_VERIFIKASI], true)) {
            $this->addError('status', __('Peminjaman pada status ini tidak dapat dibatalkan.'));

            return;
        }

        $bookingService->transisi($peminjaman, Peminjaman::DIBATALKAN, __('Dibatalkan oleh pengguna'), $user);

        session()->flash('status', __('Peminjaman berhasil dibatalkan.'));
    }

    public function render()
    {
        $user = auth()->user();

        $query = Peminjaman::query()
            ->with(['fasilitas', 'slotSesi', 'user']);

        if ($user->hasAnyRole(['admin', 'pengelola'])) {
            // Pengelola/admin: semua peminjaman.
        } else {
            $query->where('user_id', $user->id);
        }

        $query->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('tanggal')
            ->orderBy('slot_sesi_id');

        return $this->view(['daftarPeminjaman' => $query->paginate(15)])
            ->layout('layouts.app');
    }
}; ?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 fw-bold mb-0">{{ __('Peminjaman') }}</h1>

        @role('pengguna')
            <a href="{{ route('jadwal.index') }}" class="btn btn-primary">{{ __('Sewa Fasilitas') }}</a>
        @endrole
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @error('status')<div class="alert alert-danger">{{ $message }}</div>@enderror

    <select class="form-select mb-3" style="max-width: 260px;" wire:model.live="status">
        <option value="">{{ __('Semua status') }}</option>
        <option value="menunggu_pembayaran">{{ __('Menunggu Pembayaran') }}</option>
        <option value="menunggu_verifikasi">{{ __('Menunggu Verifikasi') }}</option>
        <option value="disetujui">{{ __('Disetujui') }}</option>
        <option value="dibatalkan">{{ __('Dibatalkan') }}</option>
        <option value="selesai">{{ __('Selesai') }}</option>
    </select>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">{{ __('Kode') }}</th>
                    @canany(['kelola_fasilitas', 'verifikasi_peminjaman'])
                        <th scope="col">{{ __('Penyewa') }}</th>
                    @endcanany
                    <th scope="col">{{ __('Fasilitas') }}</th>
                    <th scope="col">{{ __('Slot') }}</th>
                    <th scope="col">{{ __('Tanggal') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col" class="text-end">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPeminjaman as $peminjaman)
                    <tr wire:key="pinjam-{{ $peminjaman->id }}">
                        <td class="fw-semibold">{{ $peminjaman->kode }}</td>
                        @canany(['kelola_fasilitas', 'verifikasi_peminjaman'])
                            <td>{{ $peminjaman->user->name }}</td>
                        @endcanany
                        <td>{{ $peminjaman->fasilitas->nama }}</td>
                        <td>{{ $peminjaman->slotSesi->nama }}</td>
                        <td>{{ $peminjaman->tanggal->translatedFormat('d M Y') }}</td>
                        <td><x-status-badge :status="$peminjaman->status" /></td>
                        <td class="text-end">
                            @if (in_array($peminjaman->status, [Peminjaman::MENUNGGU_PEMBAYARAN, Peminjaman::MENUNGGU_VERIFIKASI], true))
                                <a href="{{ route('pembayaran.show', $peminjaman) }}"
                                   class="btn btn-sm btn-outline-primary">{{ __('Bayar') }}</a>

                                @role('pengguna')
                                    @if ($peminjaman->user_id === auth()->id())
                                        <button wire:click="batalkan({{ $peminjaman->id }})"
                                                wire:confirm="{{ __('Batalkan peminjaman ini?') }}"
                                                class="btn btn-sm btn-outline-danger">{{ __('Batalkan') }}</button>
                                    @endif
                                @endrole
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">{{ __('Belum ada peminjaman.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $daftarPeminjaman->links() }}
</div>