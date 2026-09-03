<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemasukan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; }
        .header p { margin: 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-box { margin-top: 15px; text-align: right; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>GOR Purnakrida — DISDIKPORA Badung</h2>
        <p>Laporan Pendapatan Sewa Fasilitas (Terverifikasi)</p>
        <p>Periode: {{ $mulai }} s/d {{ $sampai }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Tgl Verifikasi</th>
                <th>Penyewa</th>
                <th>Fasilitas</th>
                <th>Metode</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $pem)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $pem->peminjaman->kode }}</td>
                    <td>{{ $pem->verified_at?->format('d/m/Y H:i') }}</td>
                    <td>{{ $pem->peminjaman->user->name }}</td>
                    <td>{{ $pem->peminjaman->fasilitas->nama }}</td>
                    <td>{{ ucfirst($pem->metode) }}</td>
                    <td class="text-right">Rp {{ number_format($pem->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align: center;">Tidak ada data pemasukan pada periode ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Total Pendapatan:</th>
                <th class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
