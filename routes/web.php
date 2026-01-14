<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriAtributController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\StokOpnameController; // Pastikan ini ada
use Illuminate\Support\Facades\Route;

// --- HALAMAN PUBLIK ---
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- HALAMAN TERKUNCI (Harus Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Resource Gudang
    Route::resource('gudangs', GudangController::class);

    // Group Gudang Spesifik
    Route::prefix('gudangs/{gudang}')->group(function () {
        
        // Dashboard, Barang, Atribut
        Route::get('/dashboard', [GudangController::class, 'dashboard'])->name('gudang.dashboard');
        Route::resource('barangs', BarangController::class);
        Route::resource('kategori-atribut', KategoriAtributController::class);
        
        // Transaksi
        Route::get('/transaksi/masuk', [TransaksiController::class, 'masuk'])->name('transaksi.masuk');
        Route::get('/transaksi/keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::post('/api/scan', [TransaksiController::class, 'handleScanQr']);

        // --- FOKUS UTAMA: STOK OPNAME ---
        
        // 1. List Riwayat (Index)
        Route::get('/opname/riwayat', [StokOpnameController::class, 'index'])->name('opname.index');
    
        // 2. Form Opname Baru (Create & Store)
        Route::get('/opname/create', [StokOpnameController::class, 'create'])->name('opname.create');
        Route::post('/opname', [StokOpnameController::class, 'store'])->name('opname.store');
    
        // 3. Detail Opname (Show)
        Route::get('/opname/{stokOpname}', [StokOpnameController::class, 'show'])->name('opname.show');
    });

});