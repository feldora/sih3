<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitikPantau extends Model
{
    protected $table = 'titik_pantau';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'nama_titik',
        'alamat',
        'latitude',
        'longitude',
        'keterangan',
        'pos_pantau_id',
        'wilayah_sungai_id',
        'kategori_id',
        'status',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    // Define any relationships or custom methods if needed
    public function posPantau()
    {
        return $this->belongsTo(PosPantau::class, 'pos_pantau_id');
    }
    public function wilayahSungai()
    {
        return $this->belongsTo(WilayahSungai::class, 'wilayah_sungai_id');
    }
    public function kategori()
    {
        return $this->belongsTo(Category::class, 'kategori_id');
    }
}
