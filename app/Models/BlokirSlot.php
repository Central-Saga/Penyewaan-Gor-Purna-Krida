<?php

namespace App\Models;

use Database\Factories\BlokirSlotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $fasilitas_id
 * @property int $slot_sesi_id
 * @property string $tanggal
 * @property string $alasan
 * @property int $diblokir_oleh
 */
#[Fillable(['fasilitas_id', 'slot_sesi_id', 'tanggal', 'alasan', 'diblokir_oleh'])]
class BlokirSlot extends Model
{
    /** @use HasFactory<BlokirSlotFactory> */
    use HasFactory;

    protected $table = 'blokir_slot';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
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
     * @return BelongsTo<User, $this>
     */
    public function diblokir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diblokir_oleh');
    }
}