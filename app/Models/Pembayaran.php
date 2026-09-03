<?php

namespace App\Models;

use Database\Factories\PembayaranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Bukti pembayaran peminjaman. Media 'bukti' di disk local (PRIVATE — Hard Rule 4).
 *
 * @property int $id
 * @property int $peminjaman_id
 * @property int $nominal
 * @property string $metode
 * @property string $status
 * @property string|null $catatan_verifikasi
 * @property int|null $diverifikasi_oleh
 * @property string|null $verified_at
 */
#[Fillable(['peminjaman_id', 'nominal', 'metode', 'status', 'catatan_verifikasi', 'diverifikasi_oleh', 'verified_at'])]
class Pembayaran extends Model implements HasMedia
{
    /** @use HasFactory<PembayaranFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public const MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    public const TERVERIFIKASI = 'terverifikasi';

    public const DITOLAK = 'ditolak';

    protected $table = 'pembayaran';

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'metode', 'nominal'])
            ->logOnlyDirty()
            ->useLogName('pembayaran');
    }

    public function registerMediaCollections(): void
    {
        // Hard Rule 4: bukti pembayaran WAJIB private (disk local, bukan public).
        $this->addMediaCollection('bukti')
            ->singleFile()
            ->useDisk('local');
    }

    /**
     * @return BelongsTo<Peminjaman, $this>
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }
}
