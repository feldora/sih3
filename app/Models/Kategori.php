<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'slug',
    ];

    public $timestamps = true;

    public function titikPantau()
    {
        return $this->hasMany(TitikPantau::class, 'kategori_id');
    }

}
