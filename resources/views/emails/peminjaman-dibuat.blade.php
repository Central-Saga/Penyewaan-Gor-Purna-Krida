@extends('emails.layout')

@section('judul', 'Pengajuan Peminjaman Dibuat')
@section('subjudul', 'Pengajuan peminjaman berhasil dibuat — menunggu verifikasi pengelola')

@section('konten')
    <p style="margin:0 0 16px; font-size:14px;">Halo <strong>{{ $peminjaman->user->name }}</strong>,</p>
    <p style="margin:0 0 16px; font-size:14px; line-height:1.6;">
        Pengelola akan memverifikasi surat resmi serta jadwal yang Anda ajukan. Jika disetujui, Anda akan menerima email untuk melakukan pembayaran.
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
    </table>

    <p style="margin:24px 0 0; font-size:14px; line-height:1.6;">
        Kami akan menginformasikan hasil verifikasi melalui email. Terima kasih.
    </p>
@endsection
