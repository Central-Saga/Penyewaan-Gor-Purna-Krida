@extends('emails.layout')

@section('judul', 'Peminjaman Dibuat')
@section('subjudul', 'Peminjaman berhasil dibuat — menunggu pembayaran')

@section('konten')
    <p style="margin:0 0 16px; font-size:14px;">Halo <strong>{{ $peminjaman->user->name }}</strong>,</p>
    <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
        Peminjaman fasilitas Anda telah kami catat. Silakan selesaikan pembayaran sebelum batas waktu agar slot tidak dilepas kembali.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; background:#f8fafc; border-radius:8px; padding:8px;">
        <tr>
            <td style="padding:8px 12px; color:#64748b; width:150px;">Kode Booking</td>
            <td style="padding:8px 12px; font-weight:bold;">{{ $peminjaman->kode }}</td>
        </tr>
        <tr>
            <td style="padding:8px 12px; color:#64748b;">Fasilitas</td>
            <td style="padding:8px 12px;">{{ $peminjaman->fasilitas->nama }}</td>
        </tr>
        <tr>
            <td style="padding:8px 12px; color:#64748b;">Sesi</td>
            <td style="padding:8px 12px;">{{ $peminjaman->slotSesi->nama }} ({{ substr($peminjaman->slotSesi->jam_mulai, 0, 5) }}–{{ substr($peminjaman->slotSesi->jam_selesai, 0, 5) }} WITA)</td>
        </tr>
        <tr>
            <td style="padding:8px 12px; color:#64748b;">Tanggal</td>
            <td style="padding:8px 12px;">{{ $peminjaman->tanggal->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="padding:8px 12px; color:#64748b;">Nominal</td>
            <td style="padding:8px 12px; font-weight:bold;">Rp {{ number_format($peminjaman->fasilitas->tarif_per_sesi, 0, ',', '.') }}</td>
        </tr>
        @if ($peminjaman->expired_at)
            <tr>
                <td style="padding:8px 12px; color:#64748b;">Batas Pembayaran</td>
                <td style="padding:8px 12px; color:#b45309; font-weight:bold;">{{ $peminjaman->expired_at->translatedFormat('d F Y, H:i') }} WITA</td>
            </tr>
        @endif
    </table>

    <p style="margin:24px 0 8px; font-size:14px;">Silakan unggah bukti pembayaran melalui tautan berikut:</p>
    <p style="margin:0 0 8px;">
        <a href="{{ $urlPembayaran }}" style="display:inline-block; background:#0d6efd; color:#ffffff; text-decoration:none; padding:10px 20px; border-radius:999px; font-size:14px; font-weight:bold;">Buka Halaman Pembayaran</a>
    </p>
    <p style="margin:8px 0 0; font-size:12px; color:#94a3b8; word-break:break-all;">{{ $urlPembayaran }}</p>
@endsection
