<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MonitoringController;

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

    // === TAMBAHKAN BARIS INI UNTUK MENGAKTIFKAN DOWNLOAD ===
    Route::get('/datin/download', [PageController::class, 'downloadDatinRaw'])->name('datin.download');

    // Rute Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ========== BLOK UNTUK MONITORING ==========
    Route::get('/monitoring/wifi', [MonitoringController::class, 'pageWifi'])->name('monitoring.wifi');
    Route::get('/monitoring/wifi/download', [MonitoringController::class, 'downloadWifiRawData'])->name('monitoring.wifi.download');
    Route::get('/monitoring/hsi', [MonitoringController::class, 'pageHsi'])->name('monitoring.hsi');
    // ===============================================

    // Rute API

});