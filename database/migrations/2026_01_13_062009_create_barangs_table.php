<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('barangs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
        $table->string('kode_barang')->index(); // Index untuk pencarian cepat
        $table->string('nama_barang');
        $table->integer('stok_awal')->default(0);
        $table->integer('stok_sekarang')->default(0); // Akan diupdate otomatis
        $table->text('keterangan')->nullable();
        $table->boolean('has_qr')->default(false);
        $table->string('satuan')->default('pcs');
        
        // FITUR DINAMIS: Menyimpan kategori (warna, ukuran, dll) dalam format JSON
        // Contoh data: {"warna": "merah", "merk": "abc"}
        $table->json('atribut_tambahan')->nullable(); 
        
        $table->timestamps();

        // Mencegah kode barang duplikat di dalam satu gudang yang sama
        $table->unique(['gudang_id', 'kode_barang']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
