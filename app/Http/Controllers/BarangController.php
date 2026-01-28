<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use App\Models\KategoriAtribut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index(Request $request, Gudang $gudang)
{
    // 1. Query Dasar
    $query = Barang::where('gudang_id', $gudang->id);

    // 2. Logika Pencarian
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('nama_barang', 'LIKE', '%' . $search . '%')
              ->orWhere('kode_barang', 'LIKE', '%' . $search . '%');
        });
    }

    // 3. Ambil data barang (Pagination)
    $barangs = $query->latest()->paginate(10);

    // 4. AMBIL DATA KATEGORI (Ini yang hilang sebelumnya!)
    // Tanpa baris ini, View akan error "Undefined variable $kategoris"
    $kategoris = KategoriAtribut::where('gudang_id', $gudang->id)->get(); // <--- PENTING

    // 5. Kirim ke View (Perhatikan bagian compact)
    return view('barang.index', compact('gudang', 'barangs', 'kategoris')); 
}

    public function store(Request $request, Gudang $gudang)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kode_barang' => 'required|unique:barangs,kode_barang,NULL,id,gudang_id,' . $gudang->id,
            'gambar' => 'image|file|max:2048' // Validasi maks 2MB
        ]);

        $barang = new Barang();
        $barang->gudang_id = $gudang->id;
        $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->stok_awal = $request->stok_awal ?? 0;
        $barang->stok_sekarang = $request->stok_awal ?? 0;
        $barang->keterangan = $request->keterangan;
        $barang->has_qr = $request->has('has_qr');
        $barang->atribut_tambahan = $request->attr; 

        // LOGIKA UPLOAD GAMBAR
        if ($request->hasFile('gambar')) {
            // Simpan ke folder 'barangs-images' di storage public
            $path = $request->file('gambar')->store('barangs-images', 'public');
            $barang->gambar = $path;
        }

        $barang->save();

        return redirect()->route('barangs.index', $gudang->id)->with('success', 'Barang berhasil ditambahkan');
    }

    // MENAMPILKAN HALAMAN EDIT
    public function edit(Gudang $gudang, Barang $barang)
    {
        // Ambil definisi kategori (warna, ukuran, dll) agar form inputnya muncul
        $kategoris = KategoriAtribut::where('gudang_id', $gudang->id)->get();
        
        return view('barang.edit', compact('gudang', 'barang', 'kategoris'));
    }

    // UPDATE DATA (Sudah ada sebelumnya, tapi pastikan seperti ini)
    public function update(Request $request, Gudang $gudang, Barang $barang)
    {
        $request->validate([
            'nama_barang' => 'required',
            'kode_barang' => 'required|unique:barangs,kode_barang,' . $barang->id . ',id,gudang_id,' . $gudang->id,
            'gambar' => 'image|file|max:2048'
        ]);

        $barang->kode_barang = $request->kode_barang;
        $barang->nama_barang = $request->nama_barang;
        $barang->keterangan = $request->keterangan;
        $barang->atribut_tambahan = $request->attr;
        $barang->has_qr = $request->has('has_qr');

        // LOGIKA GANTI GAMBAR
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar && Storage::disk('public')->exists($barang->gambar)) {
                Storage::disk('public')->delete($barang->gambar);
            }
            // Upload baru
            $path = $request->file('gambar')->store('barangs-images', 'public');
            $barang->gambar = $path;
        }

        $barang->save();

        return redirect()->route('barangs.index', $gudang->id)->with('success', 'Data barang diperbarui');
    }

    public function destroy(Gudang $gudang, Barang $barang)
    {
        // HAPUS GAMBAR SAAT DATA DIHAPUS
        if ($barang->gambar && Storage::disk('public')->exists($barang->gambar)) {
            Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();
        return redirect()->back()->with('success', 'Barang berhasil dihapus.');
    }
    
}