<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataCurahHujan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_curah_hujan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pos_pantau_id',
        'tanggal',
        'jam',
        'curah_hujan',
        'kategori',
        'keterangan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date',
        // 'jam' => 'datetime:H:i',
        'curah_hujan' => 'decimal:2',
    ];

    /**
     * Get the pos pantau that owns the data curah hujan.
     */
    public function posPantau(): BelongsTo
    {
        return $this->belongsTo(PosPantau::class, 'pos_pantau_id');
    }

    /**
     * Scope a query to only include data between dates.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Get kategori curah hujan based on value.
     */
    public function getKategoriCurahHujan(): string
    {
        $value = $this->curah_hujan;
        
        if ($value <= 5) {
            return 'ringan';
        } elseif ($value <= 20) {
            return 'sedang';
        } elseif ($value <= 50) {
            return 'lebat';
        } else {
            return 'sangat_lebat';
        }
    }

    /**
     * Set kategori based on curah hujan value before saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->kategori = $model->getKategoriCurahHujan();
        });
    }
}
