<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // --- TEMPEL KODENYA DI SINI (DI DALAM KURUNG KURAWAL INI) ---
        Schema::create('stok_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudangs')->onDelete('cascade');
            $table->date('tanggal_opname');
            $table->string('status')->default('proses'); 
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
        // -----------------------------------------------------------
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ini otomatis ada (jangan dihapus), gunanya untuk menghapus tabel kalau error
        Schema::dropIfExists('stok_opnames');
    }
};