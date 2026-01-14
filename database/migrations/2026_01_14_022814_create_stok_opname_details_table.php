<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- TEMPEL KODENYA DI SINI JUGA ---
        Schema::create('stok_opname_details', function (Blueprint $table) {
            $table->id();
            // onDelete cascade artinya kalau data opname induk dihapus, detailnya ikut terhapus
            $table->foreignId('stok_opname_id')->constrained('stok_opnames')->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            
            $table->integer('stok_sistem'); 
            $table->integer('stok_fisik');  
            $table->integer('selisih');     
            $table->timestamps();
        });
        // -----------------------------------
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_opname_details');
    }
};