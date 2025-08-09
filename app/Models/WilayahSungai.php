<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TitikPantau;


class WilayahSungai extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'description', 'instansi_id', 'luas', 'status', 'signature'];
    public $timestamps = true;
    protected $table = 'wilayah_sungai';
    protected $primaryKey = 'id';

    // public function titikPantau()
    // {
    //     return $this->hasMany(TitikPantau::class, 'wilayah_sungai_id', 'id');
    // }

    public function kewenangan()
    {
        return $this->hasOne(Instansi::class, 'id', 'instansi_id');
    }
    
    public function feature(){
        return $this->hasOne(GeoFeature::class, 'signature', 'signature');
    }
}
