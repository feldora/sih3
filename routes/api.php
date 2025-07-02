<?php

use Illuminate\Support\Facades\Route;

    Route::apiResource('pos-pantau', App\Http\Controllers\Api\PosPantauController::class)->only(['index', 'show']);
    Route::apiResource('wilayah-sungai', App\Http\Controllers\Api\WilayahSungaiController::class)->only(['index', 'show']);
    Route::apiResource('titik-pantau', App\Http\Controllers\Api\TitikPantauController::class)->only(['index', 'show']);
    Route::apiResource('sungai', App\Http\Controllers\Api\SungaiController::class)->only(['index', 'show']);
    Route::apiResource('posts', App\Http\Controllers\Api\PostController::class)->only(['index', 'show']);
