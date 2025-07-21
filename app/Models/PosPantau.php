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
        'kabupaten_id',
        'kecamatan_id',
        'desa_id',
        'nama_pengamat',
        'tahun_pembangunan',
        'kewenangan',
        'status',
        'geo_feature_signature'
    ];

    public $timestamps = true;

    public function titikPantau()
    {
        return $this->hasMany(TitikPantau::class, 'pos_pantau_id');
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

}
