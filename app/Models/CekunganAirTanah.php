<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CekunganAirTanah extends Model
{
    use HasFactory;

    protected $table = 'cekungan_air_tanah';

    protected $fillable = [
        'nama_cat',
        'luas_cat_ha',
        'potensi_air_tanah_bebas',
        'potensi_air_tanah_tertekan',
        // 'status_pengelolaan',
        // 'jumlah_sumur',
        // 'jenis_akuifer',
        // 'kedalaman_akuifer',
        // 'kapasitas_air_tanah',
        // 'tanggal_pembaruan'
        'signature',
    ];

    protected $casts = [
        'luas_cat_ha' => 'float',
        'potensi_air_tanah_bebas' => 'float',
        'potensi_air_tanah_tertekan' => 'float',
        // 'jumlah_sumur' => 'integer',
        // 'kedalaman_akuifer' => 'float',
        // 'kapasitas_air_tanah' => 'float',
        // 'tanggal_pembaruan' => 'date',
    ];

    protected static function booted()
    {
        static::deleting(function ($CekunganAirTanah) {
            DB::transaction(function () use ($CekunganAirTanah) {
                $CekunganAirTanah->feature()->delete();
            });
        });
    }

    public function feature()
    {
        return $this->hasOne(GeoFeature::class, 'signature', 'signature');
    }

    
    /**
     * Accessor untuk kapasitas total air tanah.
     * Jika kapasitas_air_tanah kosong, hitung dari potensi bebas + tertekan.
     */
    // public function getKapasitasTotalAttribute()
    // {
    //     if (!is_null($this->kapasitas_air_tanah)) {
    //         return $this->kapasitas_air_tanah;
    //     }
    //     return ($this->potensi_air_tanah_bebas ?? 0) + ($this->potensi_air_tanah_tertekan ?? 0);
    // }
}
