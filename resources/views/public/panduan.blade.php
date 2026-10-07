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
                    <div class="mb-5" id="alur">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-primary rounded-pill px-3 py-1.5 fw-semibold">LANGKAH 1 HINGGA 9</span>
                            <h3 class="h4 fw-bold text-dark mb-0">{{ __('Tata Cara Peminjaman Fasilitas') }}</h3>
                        </div>

                        @guest
                            <div class="alert alert-primary border-0 rounded-4 small d-flex flex-wrap align-items-center gap-2">
                                <i class="bi bi-person-plus-fill fs-5"></i>
                                <span class="flex-grow-1">{{ __('Sebelum mulai, Anda perlu memiliki akun terdaftar di sistem.') }}</span>
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill" wire:navigate>
                                    {{ __('Daftar Akun Baru') }}
                                </a>
                            </div>
                        @endguest

                        <div class="d-flex flex-column gap-4">
                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">1</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Pilih Fasilitas</h5>
                                    <p class="text-secondary small mb-0">
                                        Masuk ke halaman Sewa Fasilitas, kemudian pilih fasilitas yang ingin disewa.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">2</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Pilih Tanggal Sewa</h5>
                                    <p class="text-secondary small mb-0">
                                        Tentukan tanggal yang diinginkan untuk melakukan penyewaan fasilitas.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">3</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Unduh Template Surat</h5>
                                    <p class="text-secondary small mb-2">
                                        Klik tombol Lihat/Unduh Template Surat untuk mendapatkan format surat resmi yang wajib digunakan dalam pengajuan penyewaan.
                                    </p>
                                    <div class="alert alert-warning small py-2 px-3 border-0 rounded-3 mb-2">
                                        <i class="bi bi-pencil-square me-1"></i> {{ __('Catatan: pada template surat terdapat kalimat di dalam tanda kurung yang WAJIB diganti sesuai data kegiatan Anda (tidak boleh sama persis dengan template).') }}
                                    </div>
                                    <a href="{{ asset(config('gor.template_surat')) }}" download class="btn btn-outline-primary btn-sm rounded-pill">
                                        <i class="bi bi-download me-1"></i> {{ __('Lihat / Unduh Template Surat (.docx)') }}
                                    </a>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">4</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Siapkan dan Unggah Surat</h5>
                                    <p class="text-secondary small mb-0">
                                        Buat surat sesuai dengan template yang telah disediakan, kemudian unggah surat tersebut ke dalam sistem sebagai syarat utama pengajuan.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">5</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Ajukan Jadwal Sewa</h5>
                                    <p class="text-secondary small mb-0">
                                        Setelah seluruh data dan dokumen dilengkapi, ajukan jadwal penyewaan untuk diproses oleh pengelola GOR.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">6</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Menunggu Verifikasi</h5>
                                    <p class="text-secondary small mb-2">
                                        Pengajuan akan diperiksa oleh pengelola (surat resmi serta jadwal/sesi). Silakan menunggu hingga status pengajuan mendapatkan hasil verifikasi.
                                    </p>
                                    <div class="alert alert-warning small py-2 px-3 border-0 rounded-3 mb-0">
                                        <i class="bi bi-exclamation-triangle me-1"></i> {{ __('Jika pengajuan ditolak, Anda akan menerima pesan revisi. Perbaiki surat atau jadwal lalu ajukan ulang pada pengajuan yang sama dalam batas waktu 24 jam sebelum slot dilepas otomatis.') }}
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">7</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Lakukan Pembayaran</h5>
                                    <p class="text-secondary small mb-0">
                                        Jika pengajuan telah disetujui, lanjutkan ke proses pembayaran sesuai dengan nominal yang ditentukan.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">8</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Unggah Bukti Pembayaran</h5>
                                    <p class="text-secondary small mb-0">
                                        Setelah melakukan pembayaran melalui transfer bank, unggah bukti pembayaran ke dalam sistem.
                                    </p>
                                </div>
                            </div>

                            <div class="p-4 rounded-4 bg-white border shadow-sm d-flex gap-3 align-items-start">
                                <div class="step-number flex-shrink-0">9</div>
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">Penyewaan Berhasil</h5>
                                    <p class="text-secondary small mb-0">
                                        Setelah bukti pembayaran diunggah, penyewaan dinyatakan berhasil dan jadwal fasilitas menjadi tersewa sesuai dengan tanggal dan waktu yang telah diajukan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Template Surat Resmi (HIGHLIGHTED) --}}
                    <div class="mb-5">
                        <div class="rounded-4 border border-primary border-opacity-50 bg-primary-subtle shadow-sm p-4 p-md-5" id="template-surat">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <h3 class="h4 fw-bold text-primary-emphasis mb-0">{{ __('Template Surat Permohonan Peminjaman') }}</h3>
                                <span class="badge bg-danger rounded-pill px-3 py-1.5">{{ __('WAJIB — Syarat Utama Peminjaman') }}</span>
                            </div>
                            <p class="text-secondary small mb-3">
                                {{ __('Setiap pengajuan sewa wajib melampirkan surat permohonan resmi berkop instansi. Gunakan template berikut sebagai acuan, lalu sesuaikan dengan data kegiatan Anda.') }}
                            </p>
                            <div class="alert alert-warning small py-2 px-3 border-0 rounded-3 mb-3">
                                <i class="bi bi-pencil-square me-1"></i> {{ __('Catatan: pada template terdapat kalimat di dalam tanda kurung yang WAJIB diganti (nama instansi, kegiatan, fasilitas, tanggal, jam, dan tujuan) — tidak boleh sama persis dengan template.') }}
                            </div>
                            <a href="{{ asset(config('gor.template_surat')) }}" download class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold mb-4">
                                <i class="bi bi-file-earmark-word me-1"></i> {{ __('Unduh Template Surat (.docx)') }}
                            </a>

                            {{-- Preview isi surat --}}
                            <div style="font-family: 'Times New Roman', serif; background:#fff; border:1px solid #dee2e6; border-radius:8px; padding:32px;">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div style="border:1px dashed #94a3b8; border-radius:6px; padding:18px 22px; color:#64748b; font-style:italic; text-align:center; min-width:130px;">
                                        Logo Instansi
                                    </div>
                                    <div style="text-align:right; line-height:1.5;">
                                        <div style="font-weight:bold; font-size:1.05rem;">(NAMA INSTANSI)</div>
                                        <div>(Alamat Lengkap Instansi)</div>
                                        <div>E-Mail : (E-Mail Instansi)</div>
                                        <div>Nomor Telepon (WA) : (Nomor WA Instansi)</div>
                                    </div>
                                </div>

                                <div style="text-align:right; color:#64748b; font-style:italic; margin-bottom:16px;">(Tempat, Tanggal Surat Dibuat)</div>

                                <div style="line-height:1.6; margin-bottom:16px;">
                                    <div>Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: (Nomor Surat)</div>
                                    <div>Lampiran&nbsp;&nbsp;: -</div>
                                    <div>Hal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Permohonan Tempat Peminjaman Untuk (Nama Kegiatan Instansi)</div>
                                </div>

                                <div style="line-height:1.6; margin-bottom:16px;">
                                    Kepada, Yth.<br>
                                    Kepala Dinas Pendidikan Kepemudaan Dan Olah Raga Kabupaten Badung<br>
                                    Di_<br>
                                    Tempat
                                </div>

                                <div style="margin-bottom:16px;">Dengan hormat,</div>

                                <p style="text-align:justify; line-height:1.7; margin-bottom:16px;">
                                    Sehubungan dengan dilaksanakannya <span style="color:#94a3b8; font-style:italic;">(Isi Surat — informasi Nama Kegiatan, fasilitas yang disewa, hari tanggal dan jam penyewaan, serta tujuan penyewaan)</span>
                                </p>

                                <p style="text-align:justify; line-height:1.7; margin-bottom:32px;">
                                    Demikian surat permohonan peminjaman tempat ini kami sampaikan, atas perhatiannya kami sampaikan terima kasih.
                                </p>

                                <div style="text-align:right; line-height:1.6;">
                                    <div>Hormat Kami,</div>
                                    <div style="font-weight:bold;">(Nama Instansi)</div>
                                    <div style="border:1px dashed #94a3b8; border-radius:6px; padding:22px; color:#64748b; font-style:italic; display:inline-block; margin:10px 0;">
                                        TTD & Cap Instansi
                                    </div>
                                    <div style="display:flex; justify-content:flex-end; gap:56px;">
                                        <div style="text-align:center;">
                                            <div>Ketua</div>
                                            <div style="color:#64748b; font-style:italic;">(Nama Lengkap Ketua)</div>
                                        </div>
                                        <div style="text-align:center;">
                                            <div>Sekretaris</div>
                                            <div style="color:#64748b; font-style:italic;">(Nama Lengkap Sekretaris)</div>
                                        </div>
                                    </div>
                                </div>

                                <div style="border-top:1px solid #dee2e6; margin-top:24px; padding-top:12px; font-size:0.9rem;">
                                    Tembusan :<br>
                                    KONI Badung<br>
                                    Arsip.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Tata Tertib Penggunaan --}}
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

                    {{-- 4. FAQ Accordion --}}
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
                                        Berapa lama batas waktu setelah pengajuan diverifikasi?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small">
                                        Setelah pengajuan disetujui pengelola, slot dikunci sementara selama batas waktu pembayaran (24 jam). Jika bukti bayar belum diunggah dalam batas waktu tersebut, slot otomatis terbuka kembali untuk pengguna lain.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        Bagaimana jika pengajuan saya ditolak?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary small">
                                        Anda akan menerima pesan revisi dari pengelola. Perbaiki surat atau jadwal dan ajukan ulang pada pengajuan yang sama dalam batas waktu 24 jam sebelum slot dilepas otomatis.
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
                            <div class="fw-bold text-dark">{{ config('gor.rekening.bank') }}</div>
                            <div class="text-muted small mt-2">Nama Pemilik Rekening</div>
                            <div class="fw-bold text-dark small">{{ config('gor.rekening.atas_nama') }}</div>
                            <div class="text-muted small mt-2">Nomor Rekening Resmi</div>
                            <div class="fw-bold text-primary fs-5 font-monospace">{{ config('gor.rekening.nomor') }}</div>
                        </div>

                        <div class="alert alert-warning small py-2 px-3 border-0 rounded-3 mb-0">
                            <i class="bi bi-shield-exclamation me-1"></i> Jangan melakukan pembayaran ke rekening perorangan atau pihak luar.
                        </div>
                    </div>

                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white mb-4">
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-1.5 fw-semibold text-uppercase small align-self-start mb-3">
                            {{ __('Dokumen Wajib') }}
                        </span>
                        <h5 class="fw-bold text-dark mb-2">Template Surat Permohonan</h5>
                        <p class="text-secondary small mb-3">
                            Unduh dan lengkapi surat permohonan resmi berkop instansi sebelum mengajukan jadwal sewa.
                        </p>
                        <a href="{{ asset(config('gor.template_surat')) }}" download class="btn btn-primary rounded-pill fw-semibold py-2 w-100">
                            <i class="bi bi-file-earmark-word me-1"></i> {{ __('Unduh Template (.docx)') }}
                        </a>
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
