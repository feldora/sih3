<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('pos-pantau', App\Http\Controllers\Api\PosPantauController::class)->only(['index', 'show']);
Route::apiResource('wilayah-sungai', App\Http\Controllers\Api\WilayahSungaiController::class)->only(['index', 'show']);
Route::apiResource('titik-pantau', App\Http\Controllers\Api\TitikPantauController::class)->only(['index', 'show']);
Route::apiResource('sungai', App\Http\Controllers\Api\SungaiController::class)->only(['index', 'show']);
Route::apiResource('posts', App\Http\Controllers\Api\PostController::class)->only(['index', 'show']);

Route::get('/geo-features/provinsi', [App\Http\Controllers\Api\GeoFeatureController::class, 'getProvinsi']);
Route::get('/geo-features/kabupaten', [App\Http\Controllers\Api\GeoFeatureController::class, 'getKabupaten']);
Route::get('/geo-features/kecamatan', [App\Http\Controllers\Api\GeoFeatureController::class, 'getKecamatan']);
Route::get('/geo-features/desa', [App\Http\Controllers\Api\GeoFeatureController::class, 'getDesa']);
Route::get('/geo-features/filter', [App\Http\Controllers\Api\GeoFeatureController::class, 'filter']);
Route::apiResource('geo-features', App\Http\Controllers\Api\GeoFeatureController::class)->only(['index','show']);
Route::get('/geospasial/search', [App\Http\Controllers\Api\GeoFeatureController::class, 'search']);

Route::get('/pos-pantau/search', [App\Http\Controllers\Api\PosPantauController::class, 'search'])->name('pos-pantau.search');