<div class="card h-100">
    @if ($fasilitas->getFirstMediaUrl('foto'))
        <img src="{{ $fasilitas->getFirstMediaUrl('foto') }}" class="card-img-top" alt="{{ $fasilitas->nama }}"
             style="height: 180px; object-fit: cover;">
    @else
        <div class="card-img-top bg-secondary-subtle d-flex align-items-center justify-content-center text-secondary"
             style="height: 180px;">
            <span class="small">{{ __('Belum ada foto') }}</span>
        </div>
    @endif
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <h5 class="card-title mb-1">{{ $fasilitas->nama }}</h5>
            @isset($showStatus) <x-status-badge :status="$fasilitas->status_aktif ? 'aktif' : 'nonaktif'" /> @endisset
        </div>
        <p class="card-subtitle text-secondary small mb-2">
            {{ ucfirst($fasilitas->jenis) }} — kapasitas {{ $fasilitas->kapasitas }} orang
        </p>
        <p class="card-text small text-truncate">{{ $fasilitas->deskripsi }}</p>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">{{ number_format($fasilitas->tarif_per_sesi, 0, ',', '.') }}</span>
        @isset($action)
            <a href="{{ $action }}" class="btn btn-sm btn-primary">{{ __('Sewa') }}</a>
        @endisset
    </div>
</div>