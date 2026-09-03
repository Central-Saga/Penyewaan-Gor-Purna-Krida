<x-layouts.public title="Sewa Lapangan Olahraga Resmi Badung">
    {{-- Hero Section --}}
    <header class="landing-hero text-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 small mb-3 backdrop-blur">
                        <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-shield-check me-1"></i>RESMI</span>
                        <span>DISDIKPORA Kabupaten Badung</span>
                    </div>

                    <h1 class="display-5 fw-black tracking-tight mb-3 text-white">
                        Sewa Lapangan Olahraga di <span class="text-info">GOR Purnakrida</span> Lebih Cepat & Praktis
                    </h1>

                    <p class="lead text-light text-opacity-75 mb-4" style="max-width: 620px;">
                        Pusat gelanggang olahraga indoor & outdoor terlengkap di Badung. Cek slot jadwal kosong secara daring, pesan langsung secara online, dan nikmati lapangan berstandar resmi.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-5">
                        @auth
                            <a href="{{ route('jadwal.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 py-2.5 fw-semibold shadow d-inline-flex align-items-center gap-2" wire:navigate>
                                <i class="bi bi-calendar2-check"></i>
                                <span>{{ __('Sewa Lapangan Sekarang') }}</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill px-4 py-2.5 fw-semibold shadow d-inline-flex align-items-center gap-2" wire:navigate>
                                <i class="bi bi-person-plus"></i>
                                <span>{{ __('Daftar & Sewa Sekarang') }}</span>
                            </a>
                        @endauth

                        <a href="{{ route('fasilitas.public') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2" wire:navigate>
                            <i class="bi bi-grid"></i>
                            <span>{{ __('Jelajahi Fasilitas') }}</span>
                        </a>
                    </div>

                    {{-- Hero Highlight Chips --}}
                    <div class="d-flex flex-wrap gap-2 pt-2">
                        <div class="stat-chip d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success"></i> Standar Lapangan Resmi
                        </div>
                        <div class="stat-chip d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-info"></i> Jadwal Online Realtime
                        </div>
                        <div class="stat-chip d-flex align-items-center gap-2">
                            <i class="bi bi-tag-fill text-warning"></i> Tarif Terstandar Perda
                        </div>
                    </div>
                </div>

                {{-- Hero Visual Card --}}
                <div class="col-lg-5">
                    <div class="hero-preview-card rounded-4 p-3 shadow-lg">
                        <div class="position-relative rounded-3 overflow-hidden mb-3" style="height: 250px;">
                            <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1000&auto=format&fit=crop"
                                 alt="Arena GOR Purnakrida" class="w-100 h-100 object-fit-cover" loading="lazy">
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge rounded-pill bg-success bg-opacity-90 px-3 py-1.5 shadow-sm backdrop-blur">
                                    <i class="bi bi-circle-fill me-1 small"></i> Buka • 08.00 - 16.00 WITA
                                </span>
                            </div>
                        </div>

                        <div class="px-2 pb-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-white mb-0">GOR Purnakrida Kerobokan</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-2.5">
                                    Badung, Bali
                                </span>
                            </div>
                            <p class="small text-light text-opacity-75 mb-3">
                                Tersedia 5 arena olahraga aktif: Badminton Indoor, Bola Basket, Bola Voli, dan Tenis Meja.
                            </p>
                            <div class="d-flex flex-wrap gap-1.5">
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 rounded-pill px-2.5 py-1 small">
                                    🏸 Badminton 1 & 2
                                </span>
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 rounded-pill px-2.5 py-1 small">
                                    🏀 Basket Indoor
                                </span>
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 rounded-pill px-2.5 py-1 small">
                                    🏐 Voli Outdoor
                                </span>
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-10 rounded-pill px-2.5 py-1 small">
                                    🏓 Tenis Meja
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Section Fasilitas Unggulan --}}
    <section class="py-5">
        <div class="container py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4">
                <div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold text-uppercase small mb-2">
                        {{ __('Fasilitas Pilihan') }}
                    </span>
                    <h2 class="h2 fw-bold text-dark mb-1">{{ __('Gelanggang & Lapangan Olahraga') }}</h2>
                    <p class="text-secondary mb-0">
                        {{ __('Fasilitas berkualitas tinggi untuk latihan, persahabatan, maupun turnamen.') }}
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('fasilitas.public') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 small fw-semibold d-inline-flex align-items-center gap-1.5" wire:navigate>
                        <span>{{ __('Lihat Semua Fasilitas') }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="row g-4">
                @forelse ($fasilitas as $f)
                    <div class="col-sm-6 col-lg-4">
                        <x-fasilitas-card :fasilitas="$f" :action="auth()->check() ? route('jadwal.index') : route('register')" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border rounded-4 text-center py-5 shadow-sm">
                            <i class="bi bi-inbox fs-1 text-secondary mb-2 d-block"></i>
                            <h5 class="fw-bold">{{ __('Belum ada fasilitas yang aktif') }}</h5>
                            <p class="text-muted small mb-0">{{ __('Silakan hubungi administrator pengelola GOR Purnakrida.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Section Cara Pemesanan --}}
    <section class="py-5 bg-white border-top border-bottom">
        <div class="container py-3">
            <div class="text-center mb-5">
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-semibold text-uppercase small mb-2">
                    {{ __('Alur Praktis') }}
                </span>
                <h2 class="h2 fw-bold text-dark mb-2">{{ __('Cara Sewa Lapangan di GOR Purnakrida') }}</h2>
                <p class="text-secondary mx-auto" style="max-width: 600px;">
                    {{ __('Proses cepat dan transparan dari pemilihan jadwal hingga verifikasi oleh pengelola.') }}
                </p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="step-card">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="step-number">1</div>
                            <i class="bi bi-calendar-event fs-2 text-primary"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">{{ __('Pilih Fasilitas & Jadwal') }}</h5>
                        <p class="text-secondary small mb-0 lh-base">
                            {{ __('Telusuri lapangan yang ingin disewa, lihat slot sesi kosong pada kalender jadwal, lalu pilih waktu yang sesuai.') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="step-card">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="step-number">2</div>
                            <i class="bi bi-receipt-cutoff fs-2 text-primary"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">{{ __('Ajukan & Bayar') }}</h5>
                        <p class="text-secondary small mb-0 lh-base">
                            {{ __('Isi data peminjaman, lakukan transfer pembayaran tarif sewa resmi, dan unggah foto bukti transfer.') }}
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="step-card">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="step-number">3</div>
                            <i class="bi bi-check2-circle fs-2 text-success"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">{{ __('Verifikasi & Main') }}</h5>
                        <p class="text-secondary small mb-0 lh-base">
                            {{ __('Pengelola memverifikasi bukti bayar. Jadwal resmi terkunci untuk Anda dan siap digunakan tepat waktu.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center pt-2">
                <a href="{{ route('panduan') }}" class="btn btn-link text-primary text-decoration-none fw-semibold d-inline-flex align-items-center gap-1.5" wire:navigate>
                    <span>{{ __('Pelajari Tata Tertib & Ketentuan Lengkap') }}</span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Section Keunggulan --}}
    <section class="py-5">
        <div class="container py-3">
            <div class="text-center mb-5">
                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1.5 fw-semibold text-uppercase small mb-2">
                    {{ __('Keunggulan Kami') }}
                </span>
                <h2 class="h2 fw-bold text-dark mb-2">{{ __('Kenyamanan & Fasilitas Terbaik') }}</h2>
                <p class="text-secondary mx-auto" style="max-width: 600px;">
                    {{ __('Fasilitas publik bermutu tinggi yang dirawat secara berkala untuk mendukung pembinaan olahraga masyarakat Badung.') }}
                </p>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrapper bg-primary-subtle text-primary">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">{{ __('Standar Lapangan Resmi') }}</h6>
                        <p class="text-secondary small mb-0">
                            {{ __('Lantai mat khusus, ring basket kokoh, serta jaring net terawat sesuai standar cabang olahraga.') }}
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrapper bg-success-subtle text-success">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">{{ __('Tarif Sewa Resmi') }}</h6>
                        <p class="text-secondary small mb-0">
                            {{ __('Tarif transparan sesuai ketetapan peraturan daerah DISDIKPORA Badung tanpa perantara.') }}
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrapper bg-info-subtle text-info">
                            <i class="bi bi-p-square"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">{{ __('Parkir & Tribun Luas') }}</h6>
                        <p class="text-secondary small mb-0">
                            {{ __('Kapasitas parkir kendaraan yang aman dan tribun penonton nyaman untuk pertandingan.') }}
                        </p>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-box">
                        <div class="feature-icon-wrapper bg-danger-subtle text-danger">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">{{ __('Lokasi Strategis') }}</h6>
                        <p class="text-secondary small mb-0">
                            {{ __('Mudah diakses dari berbagai wilayah di Kuta Utara, Badung, dan sekitarnya.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Call To Action Banner --}}
    <section class="py-5 bg-dark text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="container py-4 text-center">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-primary bg-opacity-25 text-primary-subtle border border-primary border-opacity-25 small mb-3">
                <i class="bi bi-fire"></i>
                <span>Mulai Sesi Olahraga Anda</span>
            </div>
            <h2 class="display-6 fw-bold text-white mb-3">Siap Booking Lapangan di GOR Purnakrida?</h2>
            <p class="lead text-light text-opacity-75 mx-auto mb-4" style="max-width: 600px;">
                Daftarkan akun sekarang atau lihat langsung ketersediaan slot jadwal secara daring tanpa perlu antre ke lokasi.
            </p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                @auth
                    <a href="{{ route('jadwal.index') }}" class="btn btn-primary btn-lg rounded-pill px-4 py-2.5 fw-semibold shadow" wire:navigate>
                        <i class="bi bi-calendar-check me-1"></i> {{ __('Lihat Jadwal & Pesan') }}
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill px-4 py-2.5 fw-semibold shadow" wire:navigate>
                        <i class="bi bi-person-plus me-1"></i> {{ __('Daftar Akun Pengguna') }}
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 py-2.5 fw-semibold" wire:navigate>
                        {{ __('Masuk ke Sistem') }}
                    </a>
                @endauth
            </div>
        </div>
    </section>
</x-layouts.public>
