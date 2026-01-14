<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    // Agar bisa create data tanpa error MassAssignment
    protected $guarded = ['id'];

    // RELASI 1: Satu Gudang punya banyak Barang
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    // RELASI 2: Satu Gudang punya banyak Kategori Atribut
    public function kategoriAtributs()
    {
        return $this->hasMany(KategoriAtribut::class);
    }
}