<?php

namespace App\Models;

use Database\Factories\FasilitasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
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
     * URL foto fasilitas, fallback ke foto Unsplash bertema olahraga jika belum ada unggahan.
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->hasMedia('foto')) {
            return $this->getFirstMediaUrl('foto');
        }

        $nama = strtolower($this->nama);

        return match (true) {
            str_contains($nama, 'badminton 2') => 'https://images.unsplash.com/photo-1587280501635-68a0e82cd5ff?q=80&w=800&auto=format&fit=crop',
            str_contains($nama, 'badminton') => 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=800&auto=format&fit=crop',
            str_contains($nama, 'basket') => 'https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=800&auto=format&fit=crop',
            str_contains($nama, 'volley') || str_contains($nama, 'voli') => 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?q=80&w=800&auto=format&fit=crop',
            str_contains($nama, 'tenis meja') || str_contains($nama, 'pingpong') => 'https://images.unsplash.com/photo-1534158914592-062992fbe900?q=80&w=800&auto=format&fit=crop',
            $this->jenis === 'outdoor' => 'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?q=80&w=800&auto=format&fit=crop',
            default => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=800&auto=format&fit=crop',
        };
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
