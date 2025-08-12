<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosPantau extends Model
{
    protected $table = 'pos_pantau';
    protected $primaryKey = 'id';
    protected $fillable = [
        'jenis_pos',
        'nama_pos',
        'latitude',
        'longitude',
        'alamat',
        'ws_id',
        'kabupaten_id',
        'kecamatan_id',
        'desa_id',
        'nama_pengamat',
        'tahun_pembangunan',
        // 'kewenangan',
        'instansi_id',
        'status',
        'geo_feature_signature'
    ];

    public $timestamps = true;

    // public function titikPantau()
    // {
    //     return $this->hasMany(TitikPantau::class, 'pos_pantau_id');
    // }

    public function kewenangan()
    {
        return $this->hasOne(Instansi::class, 'id', 'instansi_id');
    }
    
    public function ws()
    {
        return $this->hasOne(WilayahSungai::class, 'id', 'ws_id');
    }
    
    public function geoFeature()
    {
        return $this->belongsTo(GeoFeature::class, 'geo_feature_signature', 'signature');
    }

    public function kecamatan()
    {
        return $this->hasOne(Kecamatan::class, 'id', 'kecamatan_id');
    }

    public function kabupaten()
    {
        return $this->hasOne(Kabupaten::class, 'id', 'kabupaten_id');
    }

    public function desa()
    {
        return $this->hasOne(Desa::class, 'id', 'desa_id');
    }

    public function dataCurahHujan()
    {
        return $this->hasMany(DataCurahHujan::class, 'pos_pantau_id');
    }

    public function dataKlimatologi()
    {
        return $this->hasMany(DataKlimatologi::class, 'pos_pantau_id');
    }

    public function dataTinggiMukaAir()
    {
        return $this->hasMany(DataTinggiMukaAir::class, 'pos_pantau_id');
    }

    protected static function booted()
    {
        static::deleting(function ($posPantau) {
            DB::transaction(function () use ($posPantau) {
                $posPantau->feature()->delete();
            });
        });
    }

}
