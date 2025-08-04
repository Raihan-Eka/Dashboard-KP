<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\MonitoringController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/home', [PageController::class, 'showHome'])->name('home');
    Route::get('/', function () {
        return redirect()->route('home');
    });

    // Rute Datin
    Route::get('/datin', [PageController::class, 'showDatin'])->name('datin');
    Route::post('/datin/upload-excel', [PageController::class, 'uploadDatinExcel'])->name('datin.upload.excel');
    Route::post('/datin/store-manual', [PageController::class, 'storeDatinManual'])->name('datin.store.manual');
    Route::get('/datin/download', [PageController::class, 'downloadDatinRaw'])->name('datin.download');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rute Monitoring WiFi
    Route::get('/monitoring/wifi', [MonitoringController::class, 'pageWifi'])->name('monitoring.wifi');
    Route::get('/monitoring/wifi/download', [MonitoringController::class, 'downloadWifiRawData'])->name('monitoring.wifi.download');
    Route::post('/monitoring/wifi/upload-excel', [MonitoringController::class, 'uploadWifiExcel'])->name('wifi.upload.excel');

    // Rute Monitoring HSI
    Route::get('/monitoring/hsi', [MonitoringController::class, 'pageHsi'])->name('monitoring.hsi');
    Route::post('/monitoring/hsi/upload-excel', [MonitoringController::class, 'uploadHsiExcel'])->name('hsi.upload.excel');
    // Rute API untuk Dropdown
    Route::get('/api/get-witel/{regional}', [MonitoringController::class, 'getWitel']);
    Route::get('/api/get-hsa/{witel}', [MonitoringController::class, 'getHsa']);
    Route::get('/api/get-workzone/{parent_type}/{parent_value}', [MonitoringController::class, 'getWorkzone']);
});