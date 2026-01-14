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
    Schema::create('transaksis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
        $table->enum('jenis', ['masuk', 'keluar']); // Hanya bisa 'masuk' atau 'keluar'
        $table->date('tanggal');
        $table->integer('qty');
        $table->string('satuan')->nullable(); // Snapshot satuan saat transaksi
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
