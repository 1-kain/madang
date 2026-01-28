<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        // Kita set default ke 1 dulu, nanti bisa di-override saat eksekusi
        'gudang_id' => 1, 
        
        // Buat kode unik, misal: BRG-3842
        'kode_barang' => 'BRG-' . $this->faker->unique()->numberBetween(1000, 9999),
        
        // Buat nama barang acak 3 kata, misal: "Meja Kayu Jati"
        'nama_barang' => $this->faker->words(3, true),
        
        // Stok acak antara 10 sampai 100
        'stok_awal' => $this->faker->numberBetween(10, 100),
        'stok_sekarang' => $this->faker->numberBetween(10, 100),
        
        'satuan' => 'Pcs',
        'keterangan' => 'Data dummy otomatis',
        'has_qr' => true,
        'atribut_tambahan' => [], // Kosongkan dulu biar aman
    ];
}
}
