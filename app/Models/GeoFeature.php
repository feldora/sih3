<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoFeature extends Model
{
    protected $table = 'geo_features';

    protected $fillable = [
        'name',
        'properties',
        'geom',
    ];

    protected $casts = [
        'properties' => 'array',
    ];
}
