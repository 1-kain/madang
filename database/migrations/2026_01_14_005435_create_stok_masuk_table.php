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
    Schema::create('stok_masuk', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel gudang & barang
        $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
        $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
        
        // Data transaksi
        $table->integer('jumlah_masuk'); // Jumlah barang masuk
        $table->date('tanggal_masuk')->nullable(); // Opsional, atau pakai created_at saja
        $table->text('keterangan')->nullable(); // Catatan tambahan
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_masuk');
    }
};
