<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\UserManagementController;
use App\Http\Controllers\Web\MenuController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\TagController;
use App\Http\Controllers\Web\MediaController;
use App\Http\Controllers\Web\WilayahSungaiController;
use App\Http\Controllers\Web\TitikPantauController;
use App\Http\Controllers\Web\AksesRoleController;
use App\Http\Controllers\Web\PosPantauController;
use App\Http\Controllers\Web\SungaiController;
use App\Http\Controllers\Web\LoadShpController;
use App\Http\Controllers\Web\FormFieldsController;
use App\Http\Controllers\Web\DataHujanController;
use App\Http\Controllers\Web\DataTinggiMukaAirController;
use App\Http\Controllers\Web\DataKlimatologiController;
use App\Http\Controllers\Web\DataMukaAirTanahController;

// $routeData = config('resources');

// \Log::info("data config", $routeData);
// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/media/{path}', function ($path) {
    if (!Storage::disk('media')->exists($path)) {
        abort(404);
    }
    return response()->file(Storage::disk('media')->path($path));
})->where('path', '.*');

// public route
Route::view('/informasi-h3', 'pages.blank_page');
Route::get('/informasi-h3/info', [ App\Http\Controllers\Web\InfoH3Controller::class, 'index' ] );

Route::view('/informasi-h3/data', 'pages.blank_page');
Route::view('/informasi-h3/neraca-air', 'pages.blank_page');
Route::view('/produk-hukum', 'pages.blank_page');
Route::view('/kontak', 'pages.blank_page');
Route::view('/tentang', 'pages.tentang');
// Route::view('/artikel', 'pages.blank_page');
Route::view('/berita', 'pages.blank_page');
Route::view('/geospasial', 'pages.geospasial.index');
Route::view('/geospasial/peta', 'pages.geospasial.index');
Route::view('/geospasial/monitoring', 'pages.geospasial.index');

Route::get('/artikel', [PostController::class, 'publicIndex'])->name('artikel.publicIndex');
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
    // Route::get('import-excel', [PosPantauController::class, 'importExcel'])->name('pos-pengamatan.import-excel');
    // Route::post('import-excel', [PosPantauController::class, 'prcImport'])->name('pos-pengamatan.import-excel');
    Route::get('import-excel', [PosPantauController::class, 'importExcel'])->name('pos-pengamatan.import-excel.form');
    Route::post('import-excel', [PosPantauController::class, 'prcImport'])->name('pos-pengamatan.import-excel.process');

    Route::get('import-excel/template', [PosPantauController::class, 'templateExcel'])->name('pos-pengamatan.import-excel.template');
    
    Route::resource('sungai', SungaiController::class)->names([
        'index' => 'sungai.index',
        'create' => 'sungai.create',
        'store' => 'sungai.store',
        'show' => 'sungai.show',
        'edit' => 'sungai.edit',
        'update' => 'sungai.update',
        'destroy' => 'sungai.destroy',
    ]);
    Route::prefix('sungai/{sungai}/media')->name('sungai.media.')->group(function () {
        Route::get('{media}/download', [SungaiController::class, 'downloadMedia'])->name('download');
        Route::delete('{media}', [SungaiController::class, 'deleteMedia'])->name('delete');
        Route::get('/', [SungaiController::class, 'listMedia'])->name('list');
    });

    Route::resource('loadshp', LoadShpController::class);
    Route::post('loadshp/saveGeo', [LoadShpController::class, 'saveGeo'])->name('loadshp.saveGeo');
    Route::get('formFields', [FormFieldsController::class, 'getFields'])->name('formFields');
    Route::get('/blank', function () {
        return view('admin.blank');
    })->name('blank');

    // Data Meteorologi Routes
    Route::prefix('meteorologi')->name('meteorologi.')->group(function () {
        Route::get('/', function () {
            return view('admin.blank');
        })->name('index');

        // curah-hujan
        Route::prefix('curah-hujan')->name('curah-hujan.')->group(function () {
            Route::get('import-excel', [DataHujanController::class, 'importExcel'])->name('import-excel.form');
            Route::post('import-excel', [DataHujanController::class, 'prcImport'])->name('import-excel.process');
            Route::get('import-excel/template', [DataHujanController::class, 'templateExcel'])->name('import-excel.template');

            Route::resource('/', DataHujanController::class)->names([
                'index' => 'index',
                'create' => 'create',
                'store' => 'store',
                'show' => 'show',
                'edit' => 'edit',
                'update' => 'update',
                'destroy' => 'destroy',
            ]);
        });

        $routeConfig = config('resources.meteorologi');
        if(!empty($routeConfig)){
            foreach ($routeConfig as $key => $resource) {
                Route::prefix($resource['prefix'])
                    ->name($resource['name'])
                    ->group(function () use ($resource) {
                        $config = $resource['config'];
                        
                        // INDEX
                        Route::get('/', function (Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->index($request, $service);
                        })->name('index');
    
                        // CREATE
                        Route::get('/create', function () use ($config) {
                            return (new DataMukaAirTanahController($config))->create();
                        })->name('create');
    
                        // STORE
                        Route::post('/', function (Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->store($request, $service);
                        })->name('store');
    
                        // SHOW
                        Route::get('/{slug}', function ($slug, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->show($slug, $service);
                        })->name('show');
    
                        // EDIT
                        Route::get('/{post}/edit', function (App\Models\Post $post) use ($config) {
                            return (new DataMukaAirTanahController($config))->edit($post);
                        })->name('edit');
    
                        // UPDATE
                        Route::put('/{post}', function (App\Models\Post $post, Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->update($post, $request, $service);
                        })->name('update');
    
                        // DESTROY
                        Route::delete('/{post}', function (App\Models\Post $post, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->destroy($post, $service);
                        })->name('destroy');
                    });
            }
        }
    });

    // Data Hidrologi Routes
    Route::prefix('hidrologi')->name('hidrologi.')->group(function () {
        Route::get('/', function () {
            return view('admin.blank');
        })->name('index');

        // tinggi-muka-air
        Route::prefix('tinggi-muka-air')->name('tma.')->group(function () {
            Route::get('import-excel', [DataTinggiMukaAirController::class, 'importExcel'])->name('import-excel.form');
            Route::post('import-excel', [DataTinggiMukaAirController::class, 'prcImport'])->name('import-excel.process');
            Route::get('import-excel/template', [DataTinggiMukaAirController::class, 'templateExcel'])->name('import-excel.template');

            Route::resource('/', DataTinggiMukaAirController::class)
                ->parameters(['' => 'dataTinggiMukaAir']) // kosong karena prefix sudah 'tinggi-muka-air'
                ->names([
                    'index' => 'index',
                    'create' => 'create',
                    'store' => 'store',
                    'show' => 'show',
                    'edit' => 'edit',
                    'update' => 'update',
                    'destroy' => 'destroy',
                ]);
        });

        // Data Klimatologi Routes
        Route::prefix('data-klimatologi')->name('klimatologi.')->group(function () {
            Route::get('import-excel', [DataKlimatologiController::class, 'importExcel'])->name('import-excel.form');
            Route::post('import-excel', [DataKlimatologiController::class, 'prcImport'])->name('import-excel.process');
            Route::get('import-excel/template', [DataKlimatologiController::class, 'templateExcel'])->name('import-excel.template');

            Route::resource('', DataKlimatologiController::class)
            ->parameters(['' => 'dataKlimatologi'])
            ->names([
                'index' => 'index',
                'create' => 'create',
                'store' => 'store',
                'show' => 'show',
                'edit' => 'edit',
                'update' => 'update',
                'destroy' => 'destroy',
            ]);
        });


        $routeConfig = config('resources.hidrologi');
        if(!empty($routeConfig)){
            foreach ($routeConfig as $key => $resource) {
                Route::prefix($resource['prefix'])
                    ->name($resource['name'])
                    ->group(function () use ($resource) {
                        $config = $resource['config'];
                        
                        // INDEX
                        Route::get('/', function (Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->index($request, $service);
                        })->name('index');

                        // CREATE
                        Route::get('/create', function () use ($config) {
                            return (new DataMukaAirTanahController($config))->create();
                        })->name('create');

                        // STORE
                        Route::post('/', function (Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->store($request, $service);
                        })->name('store');

                        // SHOW
                        Route::get('/{slug}', function ($slug, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->show($slug, $service);
                        })->name('show');

                        // EDIT
                        Route::get('/{post}/edit', function (App\Models\Post $post) use ($config) {
                            return (new DataMukaAirTanahController($config))->edit($post);
                        })->name('edit');

                        // UPDATE
                        Route::put('/{post}', function (App\Models\Post $post, Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->update($post, $request, $service);
                        })->name('update');

                        // DESTROY
                        Route::delete('/{post}', function (App\Models\Post $post, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->destroy($post, $service);
                        })->name('destroy');
                    });
            }
        }

    });

    // Data Geologi Routes
    Route::prefix('geologi')->name('geologi.')->group(function () {
        Route::get('/', function () {
            return view('admin.blank');
        })->name('index');
        
        $routeConfig = config('resources.geologi');
        if(!empty($routeConfig)){
            foreach ($routeConfig as $key => $resource) {
                Route::prefix($resource['prefix'])
                    ->name($resource['name'])
                    ->group(function () use ($resource) {
                        $config = $resource['config'];                        
                        Route::get('/', function (Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->index($request, $service);
                        })->name('index');
                        Route::get('/create', function () use ($config) {
                            return (new DataMukaAirTanahController($config))->create();
                        })->name('create');
                        Route::post('/', function (Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->store($request, $service);
                        })->name('store');
                        Route::get('/{slug}', function ($slug, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->show($slug, $service);
                        })->name('show');
                        Route::get('/{post}/edit', function (App\Models\Post $post) use ($config) {
                            return (new DataMukaAirTanahController($config))->edit($post);
                        })->name('edit');
                        Route::put('/{post}', function (App\Models\Post $post, Illuminate\Http\Request $request, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->update($post, $request, $service);
                        })->name('update');
                        Route::delete('/{post}', function (App\Models\Post $post, App\Services\PostService $service) use ($config) {
                            return (new DataMukaAirTanahController($config))->destroy($post, $service);
                        })->name('destroy');
                    });
            }
        }

    });

});

// Route::prefix('api')->name('public-api.')->group(function () {
    
// }); 