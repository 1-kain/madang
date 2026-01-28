<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;
use App\Models\Transaksi;

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
        // === LOGIKA BARU MENGGUNAKAN TABEL TRANSAKSI ===
        
        // Ambil data riwayat gabungan (Masuk & Keluar) dari tabel 'transaksis'
        $history = Transaksi::with('barang')
            ->whereHas('barang', function ($query) use ($gudang) {
                $query->where('gudang_id', $gudang->id);
            })
            ->latest('tanggal')     // Urutkan tanggal terbaru
            ->latest('created_at')  // Backup sort
            ->take(10)              // Ambil 10 saja
            ->get();

        // Kita kirim variabel '$history' ke view, BUKAN '$masuk' atau '$keluar' lagi
        return view('gudang.dashboard', compact('gudang', 'history'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $request->validate([
            'nama_gudang' => 'required'
        ]);

        $gudang->update([
            'nama_gudang' => $request->nama_gudang,
            'lokasi' => $request->lokasi
        ]);

        return redirect()->back()->with('success', 'Data gudang berhasil diperbarui');
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