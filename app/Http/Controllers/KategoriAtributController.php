<?php

namespace App\Http\Controllers;

use App\Models\KategoriAtribut;
use App\Models\Gudang;
use Illuminate\Http\Request;

class KategoriAtributController extends Controller
{
    public function store(Request $request, Gudang $gudang)
    {
        $request->validate(['nama_kategori' => 'required']);
        
        KategoriAtribut::create([
            'gudang_id' => $gudang->id,
            'nama_kategori' => $request->nama_kategori
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan');
    }

    public function destroy(Gudang $gudang, KategoriAtribut $kategoriAtribut)
    {
        // Hapus data kategori dari database
        $kategoriAtribut->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->back()->with('success', 'Kolom kategori berhasil dihapus.');
    }
}