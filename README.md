# GOR Purnakrida — Sistem Peminjaman Fasilitas

Sistem Informasi Peminjaman Fasilitas GOR Purnakrida (DISDIKPORA Badung). Mencakup
katalog fasilitas publik, jadwal slot per sesi, alur peminjaman pengguna, unggah bukti
pembayaran, verifikasi oleh pengelola, panel admin (fasilitas, slot sesi, blokir slot,
pengguna), laporan + export PDF, notifikasi email via queue, dan SEO (meta/OG, sitemap.xml).

**Stack:** Laravel 13 · Livewire 4 · Bootstrap 5 · MySQL 8.4 (Laravel Sail) · Vite · Pest.

## Prasyarat

| Kebutuhan | Versi |
| --- | --- |
| PHP | 8.5 (lihat `compose.yaml` → `docker/8.5`) |
| Composer | 2.x |
| Node.js | 22 |
| Docker + Docker Compose | untuk Sail (opsional bila PHP/MySQL lokal tersedia) |

Ekstensi PHP yang dibutuhkan: `pdo_mysql`, `mbstring`, `gd`, `zip`, `intl`.

## Setup

### Opsi A — Sail / Docker (disarankan)

```bash
cp .env.example .env
./vendor/bin/sail up -d      # build & jalankan app (port dari APP_PORT, default 80) + MySQL
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run build
```

Aplikasi tersedia di `http://localhost` (ubah `APP_PORT` di `.env` bila bentrok).

### Opsi B — toolchain lokal

```bash
composer setup      # install deps, salin .env, key:generate, migrate, npm install, npm run build
```

Pastikan `.env` menunjuk ke MySQL lokal (`DB_HOST=127.0.0.1`, `DB_USERNAME`, `DB_PASSWORD`),
lalu jalankan `php artisan migrate --seed` bila perlu seed ulang.

### Akun seed default

Dibuat oleh `AdminSeeder` dari variabel `.env` (lihat `.env.example`):

| Peran | Email | Password |
| --- | --- | --- |
| Admin | `admin@gorpurnakrida.test` | `password` |
| Pengelola | `pengelola@gorpurnakrida.test` | `password` |

Ubah lewat `ADMIN_EMAIL` / `ADMIN_PASSWORD` / `PENGELOLA_EMAIL` / `PENGELOLA_PASSWORD` sebelum
`migrate --seed`. Pengguna biasa mendaftar sendiri lewat halaman registrasi.

## Perintah Pengembangan

```bash
composer dev        # jalankan server + queue listener + log (pail) + Vite secara paralel
composer test       # config:clear → Pint (lint) → PHPStan (types) → seluruh test
composer lint       # Pint, perbaiki format otomatis
composer types:check # PHPStan level 7 (batas memori 512M via phpstan-bootstrap.php)
composer ci:check   # alias composer test (dipakai CI)
```

Jalankan test tertentu:

```bash
php artisan test --filter=BookingTest
```

## Catatan

- **Email** memakai queue (`QUEUE_CONNECTION=database`); semua Mailable mengimplement
  `ShouldQueue`. Driver default dev `MAIL_MAILER=log` menulis email ke `laravel.log`.
  Jalankan `composer dev` atau `php artisan queue:listen` agar email terkirim.
- **Test environment** (`phpunit.xml`) memakai `MAIL_MAILER=array` dan `QUEUE_CONNECTION=sync`.
- Satu test 2FA di-skip secara sengaja karena fitur tersebut tidak diaktifkan di
  `config/fortify.php` (hanya `registration` + `resetPasswords`).
