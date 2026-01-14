<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    // 1. IZINKAN PENYIMPANAN DATA
    // Kita gunakan $guarded kosong agar semua kolom bisa diisi
    // Ini penting agar fungsi Transaksi::create() di fitur Scan QR tidak gagal
    protected $guarded = [];

    // 2. FORMAT TANGGAL
    // Agar tanggal bisa dibaca oleh View Dashboard
    protected $casts = [
        'tanggal' => 'datetime',
    ];

    // 3. HUBUNGKAN KE BARANG (WAJIB ADA)
    // Tanpa ini, Dashboard tidak akan menampilkan apa-apa karena
    // dia tidak tahu transaksi ini milik barang apa (dan gudang mana)
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}