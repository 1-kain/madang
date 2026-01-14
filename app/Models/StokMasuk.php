<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMasuk extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel di database Anda (biasanya 'stok_masuk' atau 'stok_masuks')
    protected $table = 'stok_masuk'; 

    protected $guarded = ['id'];

    // Relasi: Data stok ini milik Barang siapa?
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}