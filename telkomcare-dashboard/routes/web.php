<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MonitoringController; // <-- TAMBAHKAN INI

// Rute untuk menampilkan form login (hanya untuk tamu)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
// Rute untuk memproses data login
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');

// Grup rute yang hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
    // Rute utama setelah login adalah /home
    Route::get('/home', [PageController::class, 'showHome'])->name('home');
    
    // Arahkan URL root (/) ke halaman home juga
    Route::get('/', function () {
        return redirect()->route('home');
    });

    // Halaman Datin
    Route::get('/datin', [PageController::class, 'showDatin'])->name('datin');

    // Rute Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ========== BLOK BARU UNTUK MONITORING ==========
    // Rute untuk halaman Pemantauan Wifi
    Route::get('/monitoring/wifi', [MonitoringController::class, 'pageWifi'])->name('monitoring.wifi');
    // Rute untuk halaman Pemantauan HSI
    Route::get('/monitoring/hsi', [MonitoringController::class, 'pageHsi'])->name('monitoring.hsi');
    // ===============================================

    // Rute API
    Route::prefix('api')->group(function () {
        Route::get('/regions-cities', [DashboardController::class, 'getRegionsAndCities']);
        Route::get('/dashboard-data', [DashboardController::class, 'getDashboardData']);
        Route::post('/dashboard-data', [DashboardController::class, 'storeData']);
    });
});