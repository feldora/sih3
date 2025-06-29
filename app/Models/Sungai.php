<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sungai extends Model
{
    protected $table = 'sungai';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_sungai',
        'panjang_sungai',
        'luas_das',
        'hasil_uji_kualitas_air',
        'geojson',
        'status',
        'wilayah_sungai_id'
    ];

    public $timestamps = true;

    // public function titikPantau()
    // {
    //     return $this->hasMany(TitikPantau::class, 'sungai_id');
    // }
    // public function posPantau()
    // {
    //     return $this->hasMany(PosPantau::class, 'sungai_id');
    // }
    public function wilayahSungai()
    {
        return $this->belongsTo(WilayahSungai::class, 'wilayah_sungai_id');
    }

}