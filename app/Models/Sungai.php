<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Sungai extends Model implements HasMedia
{

    use HasFactory;
    use InteractsWithMedia;
    
    protected $table = 'sungai';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_sungai',
        'panjang_sungai',
        'luas_das',
        'ordo',
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