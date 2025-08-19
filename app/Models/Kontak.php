<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'kontak';

    protected $fillable = [
        'instansi_id',
        'nama',
        'alamat',
        'email',
        'telp',
        'website',
        'keterangan',
    ];

    /**
     * Hubungan ke model Instansi (inverse).
     */
    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }
}
