<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosPantau extends Model
{
    protected $table = 'pos_pantau';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'nama_pos',
        'alamat',
        'latitude',
        'longitude',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    public $timestamps = true;

    // Define any relationships or custom methods if needed
}
