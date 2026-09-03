<x-layouts.public title="Tentang GOR Purnakrida - DISDIKPORA Badung">
    {{-- Page Header --}}
    <header class="bg-dark text-white py-5" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none" wire:navigate>{{ __('Beranda') }}</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ __('Tentang Kami') }}</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 fw-bold text-white mb-2">{{ __('Mengenal GOR Purnakrida') }}</h1>
                    <p class="lead text-light text-opacity-75 mb-0" style="max-width: 650px;">
                        {{ __('Gelanggang Olahraga terpadu kebanggaan masyarakat Kabupaten Badung yang berada di bawah naungan Dinas Pendidikan, Kepemudaan dan Olahraga.') }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    {{-- Main About Section --}}
    <section class="py-5">
        <div class="container py-2">
            <div class="row align-items-center g-5 mb-5">
                <div class="col-lg-6">
                    <div class="position-relative rounded-4 overflow-hidden shadow-lg" style="height: 380px;">
                        <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=1200&auto=format&fit=crop"
                             alt="Gedung GOR Purnakrida" class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white" style="background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.8) 100%);">
                            <span class="small fw-semibold"><i class="bi bi-geo-alt-fill text-warning me-1"></i> Kerobokan Kaja, Kuta Utara, Badung</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold text-uppercase small mb-2">
                        {{ __('Profil Singkat') }}
                    </span>
                    <h2 class="h2 fw-bold text-dark mb-3">{{ __('Pusat Pembinaan & Rekreasi Olahraga Badung') }}</h2>
                    <p class="text-secondary lh-lg mb-3">
                        GOR Purnakrida dibangun dan dikelola secara berkelanjutan oleh Pemerintah Kabupaten Badung melalui DISDIKPORA untuk menyediakan ruang aktivitas fisik, kompetisi, dan silaturahmi olahraga bagi pelajar, atlet daerah, komunitas, serta masyarakat umum.
                    </p>
                    <p class="text-secondary lh-lg mb-4">
                        Dengan sistem digital ini, pengelolaan peminjaman fasilitas bertransformasi menjadi lebih transparan, akuntabel, dan bebas dari kendala antrean konvensional.
                    </p>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-white border shadow-sm">
                                <div class="fs-4 fw-bold text-primary mb-1">5+</div>
                                <div class="text-secondary small fw-medium">Lapangan Aktif Berstandar</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-white border shadow-sm">
                                <div class="fs-4 fw-bold text-success mb-1">100%</div>
                                <div class="text-secondary small fw-medium">Tarif Resmi Sesuai Perda</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fasilitas Penunjang --}}
            <div class="pt-5 border-top mb-5">
                <div class="text-center mb-5">
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-semibold text-uppercase small mb-2">
                        {{ __('Sarana Lengkap') }}
                    </span>
                    <h2 class="h2 fw-bold text-dark mb-2">{{ __('Fasilitas Penunjang Kompleks GOR') }}</h2>
                    <p class="text-secondary mx-auto" style="max-width: 600px;">
                        {{ __('Kenyamanan atlet, komunitas, serta penonton didukung oleh berbagai prasarana pendukung.') }}
                    </p>
                </div>

                <div class="row g-4">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 border-0 rounded-4 shadow-sm bg-white p-4 text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-people fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Tribun Penonton</h5>
                            <p class="text-secondary small mb-0">Tribun bertingkat yang nyaman untuk menyaksikan laga persahabatan maupun turnamen resmi.</p>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 border-0 rounded-4 shadow-sm bg-white p-4 text-center">
                            <div class="rounded-circle bg-success bg-opacity-10 text-success mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-p-circle fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Area Parkir Luas</h5>
                            <p class="text-secondary small mb-0">Tempat parkir motor dan mobil berkapasitas besar dengan akses keluar masuk yang tertib.</p>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 border-0 rounded-4 shadow-sm bg-white p-4 text-center">
                            <div class="rounded-circle bg-info bg-opacity-10 text-info mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-droplet-half fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Toilet & Ruang Ganti</h5>
                            <p class="text-secondary small mb-0">Kamar bilas, ruang ganti baju, dan toilet terpisah untuk putra dan putri yang terjaga kebersihannya.</p>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="card h-100 border-0 rounded-4 shadow-sm bg-white p-4 text-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 text-warning-emphasis mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-shop fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-2">Area Kantin & Istirahat</h5>
                            <p class="text-secondary small mb-0">Area kantin penyedia minuman dan makanan ringan untuk rehidrasi setelah berolahraga.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Naungan DISDIKPORA Banner --}}
            <div class="p-4 p-md-5 rounded-4 bg-white border shadow-sm text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 mb-3 shadow-sm" style="width: 50px; height: 50px;">
                    <i class="bi bi-building fs-3"></i>
                </div>
                <h3 class="h3 fw-bold text-dark mb-2">Dinas Pendidikan, Kepemudaan dan Olahraga</h3>
                <h5 class="fw-normal text-secondary mb-3">Pemerintah Kabupaten Badung, Bali</h5>
                <p class="text-secondary small mx-auto mb-4" style="max-width: 600px;">
                    Komitmen kami adalah menghadirkan pelayanan publik bidang olahraga yang transparan, mudah diakses masyarakat, dan mendukung gaya hidup sehat warga Badung.
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('fasilitas.public') }}" class="btn btn-primary rounded-pill px-4 py-2 small fw-semibold" wire:navigate>
                        {{ __('Lihat Fasilitas Olahraga') }}
                    </a>
                    <a href="{{ route('kontak') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 small fw-semibold" wire:navigate>
                        {{ __('Hubungi Kami') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
