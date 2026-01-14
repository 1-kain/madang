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
    Schema::table('barangs', function (Blueprint $table) {
        // Menambah kolom gambar setelah gudang_id
        $table->string('gambar')->nullable()->after('gudang_id');
    });
}

public function down()
{
    Schema::table('barangs', function (Blueprint $table) {
        $table->dropColumn('gambar');
    });
}
};
