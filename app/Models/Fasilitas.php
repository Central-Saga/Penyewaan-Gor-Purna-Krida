<?php

namespace App\Models;

use Database\Factories\FasilitasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property string $nama
 * @property string $jenis
 * @property string|null $deskripsi
 * @property int $kapasitas
 * @property int $tarif_per_sesi
 * @property bool $status_aktif
 */
#[Fillable(['nama', 'jenis', 'deskripsi', 'kapasitas', 'tarif_per_sesi', 'status_aktif'])]
class Fasilitas extends Model implements HasMedia
{
    /** @use HasFactory<FasilitasFactory> */
    use HasFactory, InteractsWithMedia, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama', 'jenis', 'kapasitas', 'tarif_per_sesi', 'status_aktif'])
            ->logOnlyDirty()
            ->useLogName('fasilitas');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('foto')
            ->singleFile()
            ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->performOnCollections('foto');
    }

    /**
     * @return HasMany<SlotSesi, $this>
     */
    public function slotSesi(): HasMany
    {
        return $this->hasMany(SlotSesi::class);
    }

    /**
     * @return HasMany<Peminjaman, $this>
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * @return HasMany<BlokirSlot, $this>
     */
    public function blokirSlot(): HasMany
    {
        return $this->hasMany(BlokirSlot::class);
    }

    /**
     * Scope fasilitas yang aktif tampil di daftar publik.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', true);
    }
}