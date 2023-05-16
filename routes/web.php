<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ApotekController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\Auth\RegisterController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/help', function () {
    return view('help', ['title'=>'Help']);
});

Route::get('/', function () {
    return view('LandingPage', ['title' => 'Home']);
});

// route::get('/', [HomeController::class, "show"]);
// route::get('/redirects', [HomeController::class, "index"]);

route::post('/logout', [HomeController::class, "logout"])->name('logout');


//Hospital Route-----------------------------------------------------------------------------
Route::get('/rekomendasirs', [HospitalController::class, 'index']);
Route::get('/hospitals/{id}', [HospitalController::class, 'show'])->name('hospital.show');

Route::get('hospital/{id}/rate', [HospitalController::class, 'createRating'])->name('hospital.createRating');
Route::post('hospital/{id}/rate', [HospitalController::class, 'storeRating'])->name('hospital.storeRating');
//End Route -----------------------------------------------------------------------------------



Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);


//Apotek Route
Route::get('/apotek', [ApotekController::class, 'index']);
Route::get('/detail/{id}', [ApotekController::class, 'show'])->name('apotek.show');

Route::get('/help', [HelpController::class, 'index'])->name('help.index');
Route::get('apotek/{id}/rate', [ApotekController::class, 'createRating'])->name('apotek.createRating');
Route::post('apotek/{id}/rate', [ApotekController::class, 'storeRating'])->name('apotek.storeRating');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/hargadanjenisobat', function () {
    return view('hargadanjenisobat');
});

Route::get('/obats', [ObatController::class, 'index']);

Route::get('/obat/{kategori}', [ObatController::class, 'kategori'])->name('obat.kategori');
Route::get('/obat/{obat}/detail', [ObatController::class, 'detail'])->name('obat.detail');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('login', [LoginController::class, 'index'])->name('login');
//Middleware Login,Logout,
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::post('login/proses', 'proses');
    Route::get('logout', 'logout');
});

//Middleware Group setelah login
Route::group(['middleware' => ['auth']], function () {
    Route::get('dokter', [DokterController::class, 'dokter_view']);

    Route::group(['middleware' => ['CekRoleMiddleware:0']], function () {
        Route::resource('/user', UserController::class);
        Route::get('riwayat-konsultasi', [UserController::class, 'riwayat_konsultasi']);
    });

    Route::group(['middleware' => ['CekRoleMiddleware:1']], function () {
        Route::resource('dashboard', AdminController::class);
        Route::get('dokter/add', [AdminController::class, 'form_tambah']);
        Route::post('dokter/save', [AdminController::class, 'form_save']);
        Route::get('dokter/edit/{id}', [AdminController::class, 'form_edit']);
        Route::post('dokter/update/{id}', [AdminController::class, 'form_update']);
        Route::get('dokter/delete/{id}', [AdminController::class, 'form_delete']);
    });

    Route::group(['middleware' => ['CekRoleMiddleware:2']], function () {
        // Route::resource('dokter', DokterController::class);
        Route::get('konsultasi/add', [DokterController::class, 'form_konsultasi']);
        Route::post('konsultasi/save', [DokterController::class, 'save_konsultasi']);
        Route::get('konsultasi/edit/{id}', [DokterController::class, 'form_konsultasi_edit']);
        Route::post('konsultasi/update/{id}', [DokterController::class, 'form_konsultasi_update']);
        Route::get('konsultasi/delete/{id}', [DokterController::class, 'form_konsultasi_delete']);
    });

});
