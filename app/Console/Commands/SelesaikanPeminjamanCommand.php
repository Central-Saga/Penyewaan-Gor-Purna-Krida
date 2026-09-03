<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Services\BookingService;
use Illuminate\Console\Command;

class SelesaikanPeminjamanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peminjaman:selesaikan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ubah status peminjaman disetujui yang tanggal sewanya sudah lewat menjadi selesai';

    /**
     * Execute the console command.
     */
    public function handle(BookingService $bookingService): int
    {
        $selesai = Peminjaman::query()
            ->where('status', Peminjaman::DISETUJUI)
            ->whereDate('tanggal', '<', today())
            ->get();

        $count = 0;
        foreach ($selesai as $peminjaman) {
            $bookingService->transisi(
                $peminjaman,
                Peminjaman::SELESAI,
                __('Tanggal sewa terlewat'),
                null,
                'cron'
            );
            $count++;
        }

        $this->info("Berhasil menyelesaikan {$count} peminjaman yang telah terlewat.");

        return self::SUCCESS;
    }
}
