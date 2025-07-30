<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataTinggiMukaAir extends Model
{
    use HasFactory;

    protected $table = 'data_tinggi_muka_air';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'pos_pantau_id',
        'tanggal',
        'jam',
        'tinggi_muka_air',
        'keterangan',
    ];

    /**
     * Tipe data konversi otomatis.
     */
    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'datetime:H:i',
        'tinggi_muka_air' => 'integer',
    ];

    /**
     * Relasi ke model PosPantau.
     */
    public function posPantau()
    {
        return $this->belongsTo(PosPantau::class, 'pos_pantau_id');
    }
}
