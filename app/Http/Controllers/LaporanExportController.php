<?php

namespace App\Http\Controllers;

use App\Services\LaporanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanExportController extends Controller
{
    public function __invoke(Request $request, LaporanService $laporanService)
    {
        if (! $request->user()?->hasRole('admin')) {
            abort(403);
        }

        $jenis = $request->query('jenis', 'peminjaman');
        $format = $request->query('format', 'csv');
        $mulai = $request->query('mulai', now()->startOfMonth()->toDateString());
        $sampai = $request->query('sampai', now()->endOfMonth()->toDateString());

        if (! in_array($jenis, ['peminjaman', 'pemasukan'], true)) {
            abort(400);
        }

        if ($format === 'csv') {
            return $this->exportCsv($laporanService, $jenis, $mulai, $sampai);
        }

        return $this->exportPdf($laporanService, $jenis, $mulai, $sampai);
    }

    private function exportCsv(LaporanService $service, string $jenis, string $mulai, string $sampai): StreamedResponse
    {
        $filename = "laporan-{$jenis}-{$mulai}-sd-{$sampai}.csv";

        return response()->streamDownload(function () use ($service, $jenis, $mulai, $sampai) {
            $handle = fopen('php://output', 'w');

            if ($jenis === 'peminjaman') {
                fputcsv($handle, ['Kode Booking', 'Penyewa', 'Fasilitas', 'Tanggal', 'Sesi', 'Status']);
                $data = $service->peminjaman($mulai, $sampai);
                foreach ($data['daftar'] as $p) {
                    fputcsv($handle, [
                        $p->kode,
                        $p->user->name,
                        $p->fasilitas->nama,
                        $p->tanggal->toDateString(),
                        $p->slotSesi->nama_sesi,
                        $p->status,
                    ]);
                }
            } else {
                fputcsv($handle, ['Kode Booking', 'Tanggal Verifikasi', 'Penyewa', 'Fasilitas', 'Metode', 'Nominal', 'Verifikator']);
                $data = $service->pemasukan($mulai, $sampai);
                foreach ($data as $pem) {
                    fputcsv($handle, [
                        $pem->peminjaman->kode,
                        $pem->verified_at?->toDateTimeString(),
                        $pem->peminjaman->user->name,
                        $pem->peminjaman->fasilitas->nama,
                        $pem->metode,
                        $pem->nominal,
                        $pem->verifikator?->name ?? '',
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function exportPdf(LaporanService $service, string $jenis, string $mulai, string $sampai)
    {
        $filename = "laporan-{$jenis}-{$mulai}-sd-{$sampai}.pdf";

        if ($jenis === 'peminjaman') {
            $data = $service->peminjaman($mulai, $sampai);
            $pdf = Pdf::loadView('laporan.pdf-peminjaman', [
                'mulai' => $mulai,
                'sampai' => $sampai,
                'data' => $data,
            ]);
        } else {
            $data = $service->pemasukan($mulai, $sampai);
            $pdf = Pdf::loadView('laporan.pdf-pemasukan', [
                'mulai' => $mulai,
                'sampai' => $sampai,
                'data' => $data,
                'total' => $data->sum('nominal'),
            ]);
        }

        return $pdf->download($filename);
    }
}
