<div class="card h-100 facility-card rounded-4 border-0 shadow-sm overflow-hidden bg-white">
    <div class="facility-img-wrapper position-relative">
        <img src="{{ $fasilitas->foto_url }}" class="facility-img" alt="{{ $fasilitas->nama }}" loading="lazy">

        <div class="position-absolute top-0 start-0 m-3">
            <span class="badge rounded-pill bg-dark bg-opacity-75 text-white fw-medium px-3 py-1.5 shadow-sm backdrop-blur">
                <i class="bi {{ $fasilitas->jenis === 'indoor' ? 'bi-building' : 'bi-sun' }} me-1 {{ $fasilitas->jenis === 'indoor' ? 'text-info' : 'text-warning' }}"></i>
                {{ ucfirst($fasilitas->jenis) }}
            </span>
        </div>

        <div class="position-absolute top-0 end-0 m-3">
            @isset($showStatus)
                <x-status-badge :status="$fasilitas->status_aktif ? 'aktif' : 'nonaktif'" />
            @else
                <span class="badge rounded-pill bg-white text-dark fw-medium px-2.5 py-1.5 shadow-sm">
                    <i class="bi bi-people-fill me-1 text-primary"></i>{{ $fasilitas->kapasitas }} Orang
                </span>
            @endisset
        </div>
    </div>

    <div class="card-body p-4 d-flex flex-column">
        <h5 class="card-title fw-bold text-dark mb-2">{{ $fasilitas->nama }}</h5>

        <div class="d-flex align-items-center gap-3 text-secondary small mb-3">
            <span class="d-flex align-items-center gap-1">
                <i class="bi bi-geo-alt text-primary"></i> Area {{ ucfirst($fasilitas->jenis) }}
            </span>
            <span class="text-muted">•</span>
            <span class="d-flex align-items-center gap-1">
                <i class="bi bi-person-check text-primary"></i> Maks {{ $fasilitas->kapasitas }} org
            </span>
        </div>

        <p class="card-text text-secondary small flex-grow-1 mb-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; line-height: 1.5;">
            {{ $fasilitas->deskripsi }}
        </p>

        <div class="pt-3 border-top d-flex justify-content-between align-items-center mt-auto">
            <div>
                <div class="text-muted small" style="font-size: 0.75rem;">Tarif Sewa</div>
                <div class="fw-bold text-primary fs-5 lh-1">
                    Rp {{ number_format($fasilitas->tarif_per_sesi, 0, ',', '.') }}
                    <span class="text-secondary fw-normal fs-6" style="font-size: 0.8rem;">/sesi</span>
                </div>
            </div>
            @isset($action)
                <a href="{{ $action }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" wire:navigate>
                    <i class="bi bi-calendar-check"></i>
                    <span>{{ __('Sewa') }}</span>
                </a>
            @endisset
        </div>
    </div>
</div>