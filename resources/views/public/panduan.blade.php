<x-layouts.public title="Panduan Pemesanan & Tata Tertib - GOR Purnakrida">
    {{-- Page Header --}}
    <header class="bg-dark text-white py-5" style="background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none" wire:navigate>{{ __('Beranda') }}</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ __('Alur & Panduan') }}</li>
                        </ol>
                    </nav>
                    <h1 class="display-6 fw-bold text-white mb-2">{{ __('Panduan Pemesanan & Tata Tertib') }}</h1>
                    <p class="lead text-light text-opacity-75 mb-0" style="max-width: 650px;">
                        {{ __('Informasi lengkap alur sewa lapangan, ketentuan transfer pembayaran resmi, serta panduan tata tertib bagi pengguna sarana GOR Purnakrida.') }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Guide Section --}}
    <section class="py-5">
        <div class="container py-2">
            <div class="row g-5">
                {{-- Left: Step by step detail --}}
                <div class="col-lg-8">
                    {{-- 1. Alur Sewa --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary rounded-pill px-3 py-1.5 fw-semibold">LANGKAH 1 HINGGA 4</span>
                            <h3 class="h4 fw-bold text-dark mb-0">{{ __('Alur Lengkap Pemesanan Lapangan') }}</h3>
                        </div>

                        <div class="d-flex flex-column gap-4">
                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">1</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Registrasi & Masuk ke Akun</h5>
                                    <p class="text-secondary small mb-2">
                                        Calon penyewa wajib memiliki akun terdaftar di sistem reservasi GOR Purnakrida. Pendaftaran hanya memerlukan nama lengkap, email aktif, dan nomor WhatsApp untuk mempermudah komunikasi status peminjaman.
                                    </p>
                                    @guest
                                        <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm rounded-pill" wire:navigate>
                                            {{ __('Daftar Akun Baru') }}
                                        </a>
                                    @endguest
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">2</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Cek Ketersediaan Slot & Ajukan Jadwal</h5>
                                    <p class="text-secondary small mb-0">
                                        Pilih fasilitas olahraga yang diinginkan (misal Badminton 1 atau Basket Indoor), tentukan tanggal, lalu pilih slot sesi waktu yang masih tersedia (berwarna hijau). Konfirmasi data tujuan peminjaman dan ajukan.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">3</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Transfer Pembayaran & Unggah Bukti</h5>
                                    <p class="text-secondary small mb-0">
                                        Setelah permohonan dibuat, lakukan pembayaran tarif sewa resmi melalui transfer bank ke rekening resmi pengelola. Ambil foto/tangkapan layar struk transfer dan unggah pada menu pembayaran sebelum batas waktu berakhir.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">4</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Verifikasi & Penggunaan Lapangan</h5>
                                    <p class="text-secondary small mb-0">
                                        Petugas loket DISDIKPORA akan memverifikasi mutasi bukti bayar Anda. Status peminjaman akan otomatis berubah menjadi <span class="badge bg-success">Disetujui</span>. Pada hari-H, Anda cukup datang dan menunjukkan data peminjaman di sistem kepada petugas lapangan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Tata Tertib Penggunaan --}}
                    <div class="mb-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1.5 fw-semibold">TATA TERTIB</span>
                            <h3 class="h4 fw-bold text-dark mb-0">{{ __('Peraturan Penggunaan Gelanggang') }}</h3>
                        </div>

                        <div class="p-4 rounded-4 bg-white border shadow-sm">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <strong class="text-dark small d-block mb-1">Sepatu Khusus Olahraga</strong>
                                            <p class="text-secondary small mb-0">Wajib menggunakan sepatu olahraga yang bersih dan tidak meninggalkan goresan (non-marking sole untuk lapangan indoor sintetis).</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <i class="bi bi-x-circle-fill text-danger fs-5 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <strong class="text-dark small d-block mb-1">Dilarang Merokok & Makanan</strong>
                                            <p class="text-secondary small mb-0">Dilarang keras merokok, membawa rokok elektrik, makanan berat, dan minuman manis ke dalam area lantai permainan.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <i class="bi bi-clock-fill text-primary fs-5 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <strong class="text-dark small d-block mb-1">Tepat Waktu Sesuai Sesi</strong>
                                            <p class="text-secondary small mb-0">Penggunaan arena harus sesuai dengan jam mulai dan berakhir sesi peminjaman yang telah disetujui pengelola.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex align-items-start gap-2.5">
                                        <i class="bi bi-shield-fill-check text-info fs-5 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <strong class="text-dark small d-block mb-1">Menjaga Fasilitas Publik</strong>
                                            <p class="text-secondary small mb-0">Kerusakan sarana atau prasarana yang diakibatkan kelalaian pengguna menjadi tanggung jawab pihak peminjam.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. FAQ Accordion --}}
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3 py-1.5 fw-semibold">FAQ</span>
                            <h3 class="h4 fw-bold text-dark mb-0">{{ __('Pertanyaan yang Sering Diajukan') }}</h3>
                        </div>

                        <div class="accordion accordion-flush bg-white rounded-4 border shadow-sm p-3" id="faqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Apakah bisa memesan lapangan secara langsung di loket?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small">
                                        Untuk transparansi jadwal dan menghindari bentrokan waktu, seluruh permohonan sewa diarahkan melalui website sistem daring ini. Petugas loket dapat membantu mendampingi jika Anda mengalami kendala digital.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Berapa lama batas waktu pembayaran setelah mengajukan jadwal?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small">
                                        Slot yang Anda pilih akan dikunci sementara selama batas waktu pembayaran yang ditentukan di sistem (biasanya 2 jam). Jika bukti bayar belum diunggah dalam batas waktu tersebut, slot otomatis terbuka kembali untuk pengguna lain.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Bagaimana jika terjadi hujan untuk lapangan outdoor (seperti Voli)?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small">
                                        Jika terjadi kondisi cuaca ekstrem atau hujan lebat yang membahayakan pemain, Anda dapat berkoordinasi langsung dengan pengelola untuk penjadwalan ulang (reschedule) ke slot lain yang tersedia.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        Apakah fasilitas ini bisa disewa oleh pihak luar Kabupaten Badung?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small">
                                        Ya! GOR Purnakrida terbuka untuk seluruh masyarakat umum, komunitas olahraga, klub, sekolah, maupun instansi swasta dan pemerintah dengan tarif resmi yang sama sesuai ketentuan DISDIKPORA Badung.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Payment Info & CTA Card --}}
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold text-uppercase small align-self-start mb-3">
                            {{ __('Informasi Rekening Resmi') }}
                        </span>

                        <h5 class="fw-bold text-dark mb-2">Rekening Penerimaan Kas Daerah</h5>
                        <p class="text-secondary small mb-3">
                            Pastikan pembayaran ditransfer hanya ke rekening resmi yang tercantum pada nota pemesanan sistem:
                        </p>

                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <div class="text-muted small">Bank Penerima</div>
                            <div class="fw-bold text-dark">Bank BPD Bali</div>
                            <div class="text-muted small mt-2">Nama Pemilik Rekening</div>
                            <div class="fw-bold text-dark small">Penerimaan Sewa GOR DISDIKPORA Badung</div>
                            <div class="text-muted small mt-2">Nomor Rekening Resmi</div>
                            <div class="fw-bold text-primary fs-5 font-monospace">010 02 02 019283 1</div>
                        </div>

                        <div class="alert alert-warning small py-2 px-3 border-0 rounded-3 mb-0">
                            <i class="bi bi-shield-exclamation me-1"></i> Jangan melakukan pembayaran ke rekening perorangan atau pihak luar.
                        </div>
                    </div>

                    <div class="card border-0 rounded-4 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                        <h5 class="fw-bold text-white mb-2">Siap Mulai Pemesanan?</h5>
                        <p class="text-white text-opacity-75 small mb-4">
                            Pilih lapangan favorit Anda dan cek jam kosong hari ini atau minggu depan.
                        </p>
                        @auth
                            <a href="{{ route('jadwal.index') }}" class="btn btn-light rounded-pill fw-semibold py-2 w-100" wire:navigate>
                                <i class="bi bi-calendar2-check me-1"></i> {{ __('Buka Jadwal Lapangan') }}
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-light rounded-pill fw-semibold py-2 w-100 mb-2" wire:navigate>
                                <i class="bi bi-person-plus me-1"></i> {{ __('Daftar Sekarang') }}
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill fw-medium py-2 w-100" wire:navigate>
                                {{ __('Masuk Akun') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
