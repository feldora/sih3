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
    ];

    public $timestamps = true;

    protected static function booted()
    {
        static::deleting(function ($sungai) {
            DB::transaction(function () use ($sungai) {
                $sungai->feature()->delete();
            });
        });
    }

    public function feature()
    {
        return $this->hasOne(GeoFeature::class, 'signature', 'signature');
    }

}