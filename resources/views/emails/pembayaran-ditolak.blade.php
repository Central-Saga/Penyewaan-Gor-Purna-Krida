@extends('emails.layout')

@section('judul', 'Bukti Pembayaran Ditolak')
@section('subjudul', 'Bukti pembayaran perlu diperbaiki')
@section('warna', '#dc3545')

@section('konten')
    <p style="margin:0 0 16px; font-size:14px;">Halo <strong>{{ $peminjaman->user->name }}</strong>,</p>
    <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
        Bukti pembayaran untuk peminjaman <strong>{{ $peminjaman->kode }}</strong> <strong>ditolak</strong> dan perlu diunggah ulang.
    </p>

    <div style="background:#fef2f2; border-left:4px solid #dc3545; border-radius:6px; padding:12px 16px; margin:0 0 16px;">
        <p style="margin:0 0 4px; font-size:12px; color:#991b1b; text-transform:uppercase; letter-spacing:0.5px;">Catatan Verifikator</p>
        <p style="margin:0; font-size:14px; color:#7f1d1d;">{{ $catatan }}</p>
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; background:#f8fafc; border-radius:8px; padding:8px;">
        <tr>
            <td style="padding:8px 12px; color:#64748b; width:150px;">Fasilitas</td>
            <td style="padding:8px 12px;">{{ $peminjaman->fasilitas->nama }}</td>
        </tr>
        <tr>
            <td style="padding:8px 12px; color:#64748b;">Tanggal</td>
            <td style="padding:8px 12px;">{{ $peminjaman->tanggal->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p style="margin:24px 0 8px; font-size:14px;">Silakan unggah ulang bukti pembayaran yang benar melalui tautan berikut:</p>
    <p style="margin:0 0 8px;">
        <a href="{{ $urlPembayaran }}" style="display:inline-block; background:#0d6efd; color:#ffffff; text-decoration:none; padding:10px 20px; border-radius:999px; font-size:14px; font-weight:bold;">Unggah Ulang Bukti</a>
    </p>
    <p style="margin:8px 0 0; font-size:12px; color:#94a3b8; word-break:break-all;">{{ $urlPembayaran }}</p>
@endsection
