<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\StokMasuk;
use App\Models\StokKeluar;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        $gudangs = Gudang::all();
        return view('gudang.index', compact('gudangs'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama_gudang' => 'required']);
        Gudang::create($request->all());
        return redirect()->back()->with('success', 'Gudang berhasil dibuat');
    }

    public function show(Gudang $gudang)
    {
        // Menampilkan Dashboard Gudang
        return view('gudang.dashboard', compact('gudang'));
    }

    public function dashboard(Gudang $gudang)
{
    // 1. Ambil data Stok Masuk (misal 10 terakhir)
    $masuk = StokMasuk::with('barang')
                ->where('gudang_id', $gudang->id)
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($item) {
                    // Kita format datanya agar seragam dengan stok keluar
                    return (object) [
                        'jenis' => 'masuk',
                        'barang' => $item->barang,
                        'qty' => $item->jumlah_masuk, // Sesuaikan dengan nama kolom di DB kamu (misal: qty, jumlah, dll)
                        'tanggal' => $item->created_at, // Sesuaikan jika ada kolom tanggal_masuk
                    ];
                });

    // 2. Ambil data Stok Keluar (misal 10 terakhir)
    $keluar = StokKeluar::with('barang')
                ->where('gudang_id', $gudang->id)
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'jenis' => 'keluar',
                        'barang' => $item->barang,
                        'qty' => $item->jumlah_keluar, // Sesuaikan dengan nama kolom di DB kamu
                        'tanggal' => $item->created_at, // Sesuaikan jika ada kolom tanggal_keluar
                    ];
                });

    // 3. Gabungkan kedua collection, urutkan tanggal descending, ambil 10 teratas
    $history = $masuk->merge($keluar)->sortByDesc('tanggal')->take(10);

    // Kirim data ke view
    return view('gudang.dashboard', compact('gudang', 'history'));
}


    public function destroy(Gudang $gudang)
    {
        // 1. Cek apakah masih ada barang di dalam gudang ini
        // Kita asumsikan relasi di model Gudang bernama 'barangs'
        if ($gudang->barangs()->count() > 0) {
            return back()->withErrors([
                'msg' => "Gagal menghapus! Gudang '{$gudang->nama_gudang}' masih memiliki stok barang. Harap kosongkan atau hapus semua barang terlebih dahulu."
            ]);
        }

        // 2. Jika kosong, lanjutkan penghapusan
        $gudang->delete();

        return redirect()->route('gudangs.index')->with('success', 'Gudang berhasil dihapus permanen.');
    }
}