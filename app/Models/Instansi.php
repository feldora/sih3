<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instansi extends Model
{
    use SoftDeletes;

    protected $fillable = ['nama', 'singkatan'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function kontak()
    {
        return $this->hasMany(Kontak::class);
    }

}
