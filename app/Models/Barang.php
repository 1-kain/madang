<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Ini kuncinya! Mengubah JSON di database menjadi Array PHP otomatis
    protected $casts = [
        'atribut_tambahan' => 'array',
        'has_qr' => 'boolean',
    ];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}