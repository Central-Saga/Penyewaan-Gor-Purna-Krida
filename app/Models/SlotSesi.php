<?php

namespace App\Models;

use Database\Factories\SlotSesiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $fasilitas_id
 * @property string $nama
 * @property string $jam_mulai
 * @property string $jam_selesai
 */
#[Fillable(['fasilitas_id', 'nama', 'jam_mulai', 'jam_selesai'])]
class SlotSesi extends Model
{
    /** @use HasFactory<SlotSesiFactory> */
    use HasFactory;

    protected $table = 'slot_sesi';

    /**
     * @return BelongsTo<Fasilitas, $this>
     */
    public function fasilitas(): BelongsTo
    {
        return $this->belongsTo(Fasilitas::class);
    }

    /**
     * Label siap tampil, mis. "Pagi (08:00–10:00)".
     */
    public function getLabelAttribute(): string
    {
        return "{$this->nama} ({$this->jam_mulai}–{$this->jam_selesai})";
    }
}
