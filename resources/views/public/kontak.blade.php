<x-layouts.public title="Kontak & Lokasi GOR Purnakrida - DISDIKPORA Badung">
    {{-- Page Header --}}
    <header class="bg-dark text-white py-5" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none" wire:navigate>{{ __('Beranda') }}</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ __('Kontak & Lokasi') }}</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 fw-bold text-white mb-2">{{ __('Kontak & Lokasi Gelanggang') }}</h1>
                    <p class="lead text-light text-opacity-75 mb-0" style="max-width: 650px;">
                        {{ __('Informasi alamat resmi, rute lokasi, jam operasional loket, serta layanan bantuan bagi calon penyewa sarana olahraga GOR Purnakrida.') }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Contact Section --}}
    <section class="py-5">
        <div class="container py-2">
            <div class="row g-5">
                {{-- Left: Details & Map --}}
                <div class="col-lg-7">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold text-uppercase small align-self-start mb-3">
                            {{ __('Informasi Lokasi') }}
                        </span>
                        <h3 class="h4 fw-bold text-dark mb-3">GOR Purnakrida Kerobokan</h3>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-primary bg-opacity-10 p-2.5 text-primary">
                                    <i class="bi bi-geo-alt-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">Alamat Kompleks Gelanggang</div>
                                    <div class="text-secondary small">Jl. Raya Kerobokan, Kerobokan Kaja, Kec. Kuta Utara, Kabupaten Badung, Bali 80361</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-info bg-opacity-10 p-2.5 text-info">
                                    <i class="bi bi-clock-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">Jam Operasional Layanan</div>
                                    <div class="text-secondary small">Senin – Sabtu: 08.00 – 16.00 WITA</div>
                                    <div class="text-muted small">Hari Minggu & Libur Nasional: Sesuai jadwal event dan sewa resmi</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-3 bg-success bg-opacity-10 p-2.5 text-success">
                                    <i class="bi bi-building-check fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold text-dark">Lembaga Pengelola Resmi</div>
                                    <div class="text-secondary small">Dinas Pendidikan, Kepemudaan dan Olahraga (DISDIKPORA) Kabupaten Badung</div>
                                </div>
                            </div>
                        </div>

                        {{-- Google Maps Embed / Link --}}
                        <div class="rounded-3 overflow-hidden border mb-3 position-relative" style="height: 280px; background: #e2e8f0;">
                            <iframe
                                width="100%"
                                height="100%"
                                style="border:0;"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://maps.google.com/maps?q=GOR+Purna+Krida+Kerobokan+Badung&t=&z=15&ie=UTF8&iwloc=&output=embed">
                            </iframe>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Navigasi langsung via aplikasi Google Maps:</span>
                            <a href="https://maps.google.com/?q=GOR+Purna+Krida+Kerobokan+Badung" target="_blank" rel="noopener noreferrer"
                               class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold d-inline-flex align-items-center gap-1.5">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span>{{ __('Buka di Google Maps') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Right: Contact Box & Quick Message --}}
                <div class="col-lg-5">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 fw-semibold text-uppercase small align-self-start mb-3">
                            {{ __('Bantuan Reservasi') }}
                        </span>
                        <h4 class="h5 fw-bold text-dark mb-2">Punya Pertanyaan Seputar Sewa?</h4>
                        <p class="text-secondary small mb-4">
                            Silakan datang ke loket kesekretariatan GOR Purnakrida pada jam kerja, atau kirimkan pertanyaan Anda melalui formulir di bawah ini.
                        </p>

                        <form onsubmit="alert('Terima kasih! Pesan Anda telah kami terima dan akan segera ditindaklanjuti oleh sekretariat GOR Purnakrida.'); return false;">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-sm rounded-3" placeholder="Masukkan nama Anda" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Alamat Email / Nomor WhatsApp</label>
                                <input type="text" class="form-control form-control-sm rounded-3" placeholder="Contoh: 08123456789" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">Subjek / Topik Pertanyaan</label>
                                <select class="form-select form-select-sm rounded-3">
                                    <option>Pertanyaan Ketersediaan Fasilitas</option>
                                    <option>Penyewaan untuk Acara / Turnamen</option>
                                    <option>Kendala Pembayaran / Verifikasi</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-dark">Pesan / Pertanyaan</label>
                                <textarea class="form-control form-control-sm rounded-3" rows="4" placeholder="Tuliskan pertanyaan Anda secara jelas..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-send me-1"></i> {{ __('Kirim Pesan') }}
                            </button>
                        </form>
                    </div>

                    <div class="p-4 rounded-4 bg-light border">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                                <i class="bi bi-headset fs-3"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Sekretariat DISDIKPORA Badung</h6>
                                <p class="text-secondary small mb-0">Senin s/d Sabtu, Pukul 08.00 - 16.00 WITA</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
