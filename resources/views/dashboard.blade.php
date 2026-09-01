<x-layouts::app :title="__('Dashboard')">
    <h1 class="h4 fw-bold mb-4">{{ __('Dashboard') }}</h1>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle text-secondary mb-2">{{ __('Fasilitas') }}</h6>
                    <p class="display-6 fw-bold mb-0">—</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle text-secondary mb-2">{{ __('Peminjaman Saya') }}</h6>
                    <p class="display-6 fw-bold mb-0">—</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-subtitle text-secondary mb-2">{{ __('Jadwal Hari Ini') }}</h6>
                    <p class="display-6 fw-bold mb-0">—</p>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-light border mt-4">
        {{ __('Dashboard per role akan terisi pada langkah implementasi berikutnya.') }}
    </div>
</x-layouts::app>