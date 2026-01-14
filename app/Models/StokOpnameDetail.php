<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpnameDetail extends Model
{
    protected $guarded = ['id'];

    public function barang() {
        return $this->belongsTo(Barang::class);
    }
}
