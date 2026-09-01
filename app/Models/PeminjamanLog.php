<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Log perubahan status peminjaman — append-only, tanpa updated_at.
 *
 * @property int $id
 * @property int $peminjaman_id
 * @property string|null $dari_status
 * @property string $ke_status
 * @property int|null $aktor_id
 * @property string $aktor_peran
 * @property string|null $catatan
 * @property string|null $created_at
 */
#[Fillable(['peminjaman_id', 'dari_status', 'ke_status', 'aktor_id', 'aktor_peran', 'catatan'])]
class PeminjamanLog extends Model
{
    public const UPDATED_AT = null;

    protected $table = 'peminjaman_logs';

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Tulis satu baris log transisi status.
     */
    public static function log(
        Peminjaman $peminjaman,
        ?string $dari,
        string $ke,
        ?string $catatan,
        ?User $aktor,
        string $aktorPeran = 'sistem',
    ): self {
        return static::query()->create([
            'peminjaman_id' => $peminjaman->id,
            'dari_status' => $dari,
            'ke_status' => $ke,
            'aktor_id' => $aktor?->id,
            'aktor_peran' => $aktor?->hasRole('admin') ?? false ? 'admin' : ($aktor?->hasRole('pengelola') ?? false ? 'pengelola' : ($aktor ? 'pengguna' : $aktorPeran)),
            'catatan' => $catatan,
        ]);
    }
}