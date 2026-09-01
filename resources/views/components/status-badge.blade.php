@props([
    'status',
])

@php
    $map = [
        // peminjaman
        'menunggu_pembayaran' => ['Menunggu Pembayaran', 'warning'],
        'menunggu_verifikasi' => ['Menunggu Verifikasi', 'info'],
        'disetujui' => ['Disetujui', 'success'],
        'dibatalkan' => ['Dibatalkan', 'secondary'],
        'selesai' => ['Selesai', 'dark'],
        // pembayaran
        'terverifikasi' => ['Terverifikasi', 'success'],
        'ditolak' => ['Ditolak', 'danger'],
        // umum
        'aktif' => ['Aktif', 'success'],
        'nonaktif' => ['Nonaktif', 'secondary'],
        'tersedia' => ['Tersedia', 'success'],
        'terisi' => ['Terisi', 'danger'],
        'diblokir' => ['Diblokir', 'danger'],
    ];

    [$label, $warna] = $map[$status] ?? [ucfirst($status), 'secondary'];
@endphp

<span {{ $attributes->merge(['class' => "badge text-bg-{$warna}"]) }}>{{ __($label) }}</span>