<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Services\BookingService;
use Illuminate\Console\Command;

class ReleaseExpiredPeminjamanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peminjaman:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lepas slot peminjaman yang kadaluarsa 24 jam belum dibayar';

    /**
     * Execute the console command.
     */
    public function handle(BookingService $bookingService): int
    {
        $expired = Peminjaman::query()
            ->where('status', Peminjaman::MENUNGGU_PEMBAYARAN)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $peminjaman) {
            $bookingService->transisi(
                $peminjaman,
                Peminjaman::DIBATALKAN,
                __('Kadaluarsa 24 jam, slot dilepas'),
                null,
                'cron'
            );
            $count++;
        }

        $this->info("Berhasil melepas {$count} peminjaman kadaluarsa.");

        return self::SUCCESS;
    }
}
