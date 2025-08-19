<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;

class ProdukHukum extends Model implements HasMedia
{

    use HasFactory;
    use InteractsWithMedia;
    
    protected $table = 'produk_hukum';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tahun',
        'judul',
        'deskripsi',
    ];
    
    public $timestamps = true;
    public $collectionName = "produk_hukum";


}