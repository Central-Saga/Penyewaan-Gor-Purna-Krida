<?php

namespace App\Models;

use Database\Factories\PeminjamanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * State machine peminjaman (WORKFLOWS §B1):
 * menunggu_pembayaran → menunggu_verifikasi → disetujui → selesai
 * dengan cabang pembatalan/pengembalian via BookingService::transisi().
 *
 * @property int $id
 * @property string $kode
 * @property int $user_id
 * @property int $fasilitas_id
 * @property int $slot_sesi_id
 * @property string $tanggal
 * @property string $status
 * @property string|null $expired_at
 * @property string|null $status_aktif
 */
#[Fillable(['kode', 'user_id', 'fasilitas_id', 'slot_sesi_id', 'tanggal', 'status', 'expired_at'])]
class Peminjaman extends Model
{
    /** @use HasFactory<PeminjamanFactory> */
    use HasFactory, LogsActivity;

    public const MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    public const DISETUJUI = 'disetujui';

    public const DIBATALKAN = 'dibatalkan';

    public const SELESAI = 'selesai';

    /**
     * Status yang mengunci slot (dipakai generated column + query bentrok).
     *
     * @var list<string>
     */
    public const STATUS_AKTIF = [
        self::MENUNGGU_PEMBAYARAN,
        self::MENUNGGU_VERIFIKASI,
        self::DISETUJUI,
    ];

    protected $table = 'peminjaman';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date:Y-m-d',
            'expired_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->useLogName('peminjaman');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Fasilitas, $this>
     */
    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class);
    }

    /**
     * @return BelongsTo<SlotSesi, $this>
     */
    public function slotSesi(): BelongsTo
    {
        return $this->belongsTo(SlotSesi::class);
    }

    /**
     * @return HasMany<Pembayaran, $this>
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * @return HasMany<PeminjamanLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(PeminjamanLog::class);
    }
}
