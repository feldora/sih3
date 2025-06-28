<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\WilayahSungaiController;
use App\Http\Controllers\TitikPantauController;
use App\Http\Controllers\AksesRoleController;
use App\Http\Controllers\PosPantauController;


// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// public route
Route::view('/informasi-h3', 'pages.blank_page');
Route::view('/informasi-h3/info', 'pages.blank_page');
Route::view('/informasi-h3/data', 'pages.blank_page');
Route::view('/informasi-h3/neraca-air', 'pages.blank_page');
Route::view('/produk-hukum', 'pages.blank_page');
Route::view('/kontak', 'pages.blank_page');
// Route::view('/artikel', 'pages.blank_page');
Route::view('/berita', 'pages.blank_page');
Route::view('/geospasial', 'pages.geospasial.index');
Route::view('/geospasial/peta', 'pages.geospasial.index');
Route::view('/geospasial/monitoring', 'pages.geospasial.index');

Route::get('/artikel',  PostController::class . '@publicIndex')->name('artikel.publicIndex');
Route::get('/artikel/{slug}',  PostController::class . '@publicShow')->name('artikel.publicShow');


// admin route
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('pages.home');
})->name('home');

// Route::get('/artikel', function () {
//     return view('pages.artikel.list');
// });


// route after authentication and verification

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/admin/dashboard');
    })->name('dashboard');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('index');
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', UserManagementController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('posts', PostController::class);
    Route::post('admin.posts.bulk-action', [PostController::class, 'bulkAction'])->name('posts.bulk-action');
    Route::resource('categories', CategoryController::class);
    Route::resource('tags', TagController::class);
    Route::resource('media', MediaController::class);
    Route::post('media/bulk-delete', [MediaController::class, 'bulkDelete'])->name('media.bulk-delete');
    Route::get('media/{media}/download', [MediaController::class, 'download'])->name('media.download');
    Route::resource('wilayah-sungai', WilayahSungaiController::class);
    Route::resource('titik-pantau', TitikPantauController::class);
    Route::resource(('profile'), ProfileController::class)->only(['edit', 'update', 'destroy'])->names([
        'edit' => 'profile.edit',
        'update' => 'profile.update',
        'destroy' => 'profile.destroy',
    ]);
    Route::resource('akses-role', AksesRoleController::class)->names([
        'index' => 'akses-role.index',
        'create' => 'akses-role.create',
        'store' => 'akses-role.store',
        'show' => 'akses-role.show',
        'edit' => 'akses-role.edit',
        'update' => 'akses-role.update',
        'destroy' => 'akses-role.destroy',
    ]);
    Route::resource('pos-pengamatan', PosPantauController::class)->names([
        'index' => 'pos-pengamatan.index',
        'create' => 'pos-pengamatan.create',
        'store' => 'pos-pengamatan.store',
        'show' => 'pos-pengamatan.show',
        'edit' => 'pos-pengamatan.edit',
        'update' => 'pos-pengamatan.update',
        'destroy' => 'pos-pengamatan.destroy',
    ]);

    Route::get('/blank', function () {
        return view('admin.blank');
    })->name('blank');
});
