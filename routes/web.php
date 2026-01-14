<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriAtributController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

// --- HALAMAN PUBLIK (Bisa diakses tanpa login) ---
Route::get('/', function () {
    return redirect()->route('login'); // Redirect root ke login
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- HALAMAN TERKUNCI (Harus Login Dulu) ---
// Kita bungkus semua route lama di dalam middleware 'auth'
Route::middleware(['auth'])->group(function () {
    
    // Resource Gudang
    Route::resource('gudangs', GudangController::class);

    // Barang & Kategori (Nested Resource)
    Route::prefix('gudangs/{gudang}')->group(function () {
        Route::resource('barangs', BarangController::class);
        Route::resource('kategori-atribut', KategoriAtributController::class);
        
// Route untuk Dashboard Gudang Spesifik
Route::get('/dashboard', [App\Http\Controllers\GudangController::class, 'dashboard'])->name('gudang.dashboard');

        // Transaksi
        Route::get('/transaksi/masuk', [TransaksiController::class, 'masuk'])->name('transaksi.masuk');
        Route::get('/transaksi/keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        
        // API Scan QR
        Route::post('/api/scan', [TransaksiController::class, 'handleScanQr']);
    });

});