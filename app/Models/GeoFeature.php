<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    public function getGeoJsonAttribute()
    {
        $geoJson = DB::table($this->getTable())
            ->select(DB::raw('ST_AsGeoJSON(geom) as geometry'))
            ->where('id', $this->id)
            ->value('geometry');

        return json_decode($geoJson, true);
    }


    public function posPantaus()
    {
        return $this->hasMany(PosPantau::class, 'geo_feature_signature', 'signature');
    }

    public function ws() {
        return $this->hasMany(WilayahSungai::class, 'signature', 'signature');        
    }

}
