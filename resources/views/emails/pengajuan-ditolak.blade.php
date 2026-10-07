@extends('emails.layout')

@section('judul', 'Pengajuan Perlu Revisi')
@section('subjudul', 'Pengajuan peminjaman perlu diperbaiki')
@section('warna', '#dc3545')

@section('konten')
    <p style="margin:0 0 16px; font-size:14px;">Halo <strong>{{ $peminjaman->user->name }}</strong>,</p>
    <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
        Pengajuan peminjaman <strong>{{ $peminjaman->kode }}</strong> belum dapat disetujui dan perlu
        <strong>direvisi</strong>. Silakan baca catatan pengelola berikut, perbaiki surat atau jadwal, lalu ajukan ulang.
    </p>

    <div style="color:#b91c1c; background:#fef2f2; border-radius:8px; padding:12px; margin:0 0 16px;">
        <p style="margin:0 0 4px; font-size:12px; color:#991b1b; text-transform:uppercase; letter-spacing:0.5px;">Catatan Pengelola</p>
        <p style="margin:0; font-size:14px; color:#7f1d1d;">{{ $catatan }}</p>
    </div>

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
    </table>

    <p style="margin:24px 0 8px; font-size:14px;">Ajukan ulang revisi Anda melalui tautan berikut (batas waktu revisi 24 jam):</p>
    <p style="margin:0 0 8px;">
        <a href="{{ $urlRevisi }}" style="display:inline-block; background:#dc3545; color:#ffffff; text-decoration:none; padding:10px 20px; border-radius:999px; font-size:14px; font-weight:bold;">Buka Halaman Revisi</a>
    </p>
    <p style="margin:8px 0 0; font-size:12px; color:#94a3b8; word-break:break-all;">{{ $urlRevisi }}</p>
@endsection
