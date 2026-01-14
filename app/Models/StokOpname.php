<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $guarded = ['id'];

    // Relasi ke Gudang
    public function gudang() {
        return $this->belongsTo(Gudang::class);
    }

    // Relasi ke Detail (Barang-barangnya)
    public function details() {
        return $this->hasMany(StokOpnameDetail::class);
    }
}
