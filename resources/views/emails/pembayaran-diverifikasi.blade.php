@extends('emails.layout')

@section('judul', 'Pembayaran Disetujui')
@section('subjudul', 'Pembayaran terverifikasi — peminjaman disetujui')
@section('warna', '#198754')

@section('konten')
    <p style="margin:0 0 16px; font-size:14px;">Halo <strong>{{ $peminjaman->user->name }}</strong>,</p>
    <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
        Pembayaran Anda telah <strong>diverifikasi</strong>. Peminjaman fasilitas berikut resmi <strong>DISETUJUI</strong>.
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
    </table>

    <p style="margin:24px 0 0; font-size:14px; line-height:1.6;">
        Mohon hadir sesuai jadwal dan tunjukkan kode booking kepada petugas. Terima kasih.
    </p>
@endsection
