<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKlimatologi extends Model
{
    use HasFactory;

    protected $table = 'data_klimatologi';

    protected $fillable = [
        'pos_pantau_id',
        'tanggal',
        'jam',
        'kecepatan_angin',
        'arah_angin',
        'kelembapan',
        'suhu',
        'curah_hujan',
        'keterangan',
    ];

    public function posPantau()
    {
        return $this->belongsTo(PosPantau::class);
    }
}
