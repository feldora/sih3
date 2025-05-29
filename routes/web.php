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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/artikels', function () {
    return view('pages.artikel.list');
});


// route after authentication and verification

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/admin/dashboard');
    })->name('dashboard');
});

Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', UserManagementController::class);

    Route::get('/blank', function () {
        return view('admin.blank');
    })->name('blank');

    Route::resource('menus', MenuController::class);
    
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified'])->group(function () {
    // Rute untuk PostController
    Route::resource('posts', PostController::class);

    // Rute untuk CategoryController
    Route::resource('categories', CategoryController::class);

    // Rute untuk TagController
    Route::resource('tags', TagController::class);
});
