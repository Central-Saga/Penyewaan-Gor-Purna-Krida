<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0 0 5px 0; font-size: 16px; }
        .header p { margin: 0; font-size: 11px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary-box { margin-bottom: 15px; }
        .summary-title { font-weight: bold; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>GOR Purnakrida — DISDIKPORA Badung</h2>
        <p>Laporan Riwayat Peminjaman Fasilitas</p>
        <p>Periode: {{ $mulai }} s/d {{ $sampai }}</p>
    </div>

    <table style="margin-bottom: 15px;">
        <tr>
            <th colspan="2">Rekap Status</th>
            <th colspan="2">Rekap Fasilitas</th>
        </tr>
        <tr>
            <td style="vertical-align: top;">
                @foreach ($data['perStatus'] as $st => $cnt)
                    <div>{{ ucfirst(str_replace('_', ' ', $st)) }}: <strong>{{ $cnt }}</strong></div>
                @endforeach
            </td>
            <td style="vertical-align: top;"></td>
            <td style="vertical-align: top;" colspan="2">
                @foreach ($data['perFasilitas'] as $fn => $cnt)
                    <div>{{ $fn }}: <strong>{{ $cnt }}</strong></div>
                @endforeach
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Penyewa</th>
                <th>Fasilitas</th>
                <th>Tanggal & Sesi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['daftar'] as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->kode }}</td>
                    <td>{{ $p->user->name }}</td>
                    <td>{{ $p->fasilitas->nama }}</td>
                    <td>{{ $p->tanggal->toDateString() }} ({{ $p->slotSesi->nama_sesi }})</td>
                    <td>{{ $p->status }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
