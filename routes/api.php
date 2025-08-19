<?php

use Illuminate\Support\Facades\Route;
use App\Models\Kecamatan;
use App\Models\Desa;


Route::apiResource('pos-pantau', App\Http\Controllers\Api\PosPantauController::class)->only(['index', 'show']);
Route::apiResource('wilayah-sungai', App\Http\Controllers\Api\WilayahSungaiController::class)->only(['index', 'show']);
Route::apiResource('titik-pantau', App\Http\Controllers\Api\TitikPantauController::class)->only(['index', 'show']);
Route::apiResource('sungai', App\Http\Controllers\Api\SungaiController::class)->only(['index', 'show']);
Route::apiResource('posts', App\Http\Controllers\Api\PostController::class)->only(['index', 'show']);

Route::get('/geo-features/provinsi', [App\Http\Controllers\Api\GeoFeatureController::class, 'getProvinsi']);

Route::get('/geo-features/kabupaten', [App\Http\Controllers\Api\GeoFeatureController::class, 'getKabupaten']);
Route::get('/geo-features/map-kabupaten', [App\Http\Controllers\Api\GeoFeatureController::class, 'getMapKabupaten']);
Route::get('/geo-features/map-kabupaten/info', [App\Http\Controllers\Api\GeoFeatureController::class, 'getMapKabupatenInfo']);

Route::get('/geo-features/map-ws', [App\Http\Controllers\Api\GeoFeatureController::class, 'getMapWilayahSungai']);
Route::get('/geo-features/map-cat', [App\Http\Controllers\Api\GeoFeatureController::class, 'getMapCAT']);

Route::get('/geo-features/map-sungai', [App\Http\Controllers\Api\GeoFeatureController::class, 'getMapSungai']);

Route::post('/geo-features/loadshp', [App\Http\Controllers\Api\GeoFeatureController::class, 'loadshp']);

Route::get('/geo-features/kecamatan', [App\Http\Controllers\Api\GeoFeatureController::class, 'getKecamatan']);
Route::get('/geo-features/desa', [App\Http\Controllers\Api\GeoFeatureController::class, 'getDesa']);
Route::get('/geo-features/filter', [App\Http\Controllers\Api\GeoFeatureController::class, 'filter']);
Route::apiResource('geo-features', App\Http\Controllers\Api\GeoFeatureController::class)->only(['index','show','post']);
Route::get('/geospasial/search', [App\Http\Controllers\Api\GeoFeatureController::class, 'search']);

Route::get('/pos-pantau/search', [App\Http\Controllers\Api\PosPantauController::class, 'search'])->name('pos-pantau.search');


Route::get('kecamatan', function(Illuminate\Http\Request $req){
    return Kecamatan::where('kabupaten_id', $req->kabupaten_id)
        ->select('id','nama')->get();
});
Route::get('desa', function(Illuminate\Http\Request $req){
    return Desa::where('kecamatan_id', $req->kecamatan_id)
        ->select('id','nama')->get();
});

Route::get('produk-hukum/get-data', [App\Http\Controllers\Web\ProdukHukumController::class, 'getData'])->name('produk-hukum.getData');
Route::get('kontak/data', [App\Http\Controllers\Web\KontakController::class, 'getPublicData'])->name('kontak.getData');
