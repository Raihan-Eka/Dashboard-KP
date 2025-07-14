<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rute utama (/) akan otomatis mengarahkan pengguna.
// Jika belum login -> diarahkan ke halaman login.
// Jika sudah login -> diarahkan ke halaman home.
Route::get('/', function () {
    if (Auth::guest()) {
        return redirect()->route('login');
    }
    return redirect()->route('home');
});


// --- RUTE UNTUK PENGGUNA YANG BELUM LOGIN (GUEST) ---
Route::middleware('guest')->group(function () {
    // Rute untuk MENAMPILKAN form login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    // Rute untuk MEMPROSES data login dari form
    Route::post('/login', [LoginController::class, 'login']);
});


// --- RUTE YANG HANYA BISA DIAKSES SETELAH LOGIN ---
Route::middleware('auth')->group(function () {

    // Halaman Home setelah login
    Route::get('/home', [PageController::class, 'showHome'])->name('home');
    
    // Halaman Datin
    Route::get('/datin', [PageController::class, 'showDatin'])->name('datin');

    // Rute untuk proses logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rute API tetap aman di sini.
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/regions-cities', [DashboardController::class, 'getRegionsAndCities'])->name('regions-cities');
        Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData'])->name('dashboard-data');
        Route::post('/dashboard-data', [DashboardController::class, 'storeData'])->name('store-data');
    });

});
