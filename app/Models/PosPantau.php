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
        'kabupaten',
        'kecamatan',
        'desa',
        'nama_pengamat',
        'tahun_pembangunan',
        'kewenangan',
        'status'
    ];

    public $timestamps = true;

    public function titikPantau()
    {
        return $this->hasMany(TitikPantau::class, 'pos_pantau_id');
    }
}
