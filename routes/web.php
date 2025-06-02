<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;


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
Route::view('/geospasial', 'pages.blank_page');
Route::view('/geospasial/peta', 'pages.blank_page');
Route::view('/geospasial/monitoring', 'pages.blank_page');
Route::view('/produk-hukum', 'pages.blank_page');
Route::view('/kontak', 'pages.blank_page');
// Route::view('/artikel', 'pages.blank_page');
Route::view('/berita', 'pages.blank_page');

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
});

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
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', UserManagementController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('posts', PostController::class);
    Route::post('admin.posts.bulk-action', [PostController::class, 'bulkAction'])->name('posts.bulk-action');
    Route::resource('categories', CategoryController::class);
    Route::resource('tags', TagController::class);

    Route::get('/blank', function () {
        return view('admin.blank');
    })->name('blank');
});
