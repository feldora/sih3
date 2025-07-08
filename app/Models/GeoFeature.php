<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoFeature extends Model
{
    protected $table = 'geo_features';

    protected $fillable = [
        'name',
        'properties',
        'tag',
        'geom',
        'signature'
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function posPantaus()
    {
        return $this->hasMany(PosPantau::class, 'geo_feature_signature', 'signature');
    }

}
