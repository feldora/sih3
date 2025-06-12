<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TitikPantau;


class WilayahSungai extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name', 'description', 'status', 'geojson'];
    public $timestamps = true;
    protected $table = 'wilayah_sungai';
    protected $primaryKey = 'id';

    public function titikPantau()
    {
        return $this->hasMany(TitikPantau::class, 'wilayah_sungai_id', 'id');
    }
}
