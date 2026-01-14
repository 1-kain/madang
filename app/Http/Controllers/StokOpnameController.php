<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\StokOpname;
use App\Models\StokOpnameDetail;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    // 1. Tampilkan Form Input
    public function create(Gudang $gudang)
    {
        // Ambil semua barang di gudang ini
        $barangs = Barang::where('gudang_id', $gudang->id)->get();
        
        return view('opname.create', compact('gudang', 'barangs'));
    }

    // 2. Proses Simpan & Update Stok
    public function store(Request $request, Gudang $gudang)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'fisik' => 'required|array', // Array berisi ID barang => Jumlah Fisik
        ]);

        // Gunakan Transaction agar data aman
        DB::transaction(function () use ($request, $gudang) {
            
            // A. Buat Header Opname
            $opname = StokOpname::create([
                'gudang_id' => $gudang->id,
                'tanggal_opname' => $request->tanggal,
                'catatan' => $request->catatan,
                'status' => 'selesai',
            ]);

            // B. Looping setiap barang yang diinput
            foreach ($request->fisik as $barangId => $jumlahFisik) {
                
                $barang = Barang::findOrFail($barangId);
                
                // --- PERBAIKAN 1: Gunakan nama kolom yang benar (stok_sekarang) ---
                // Pakai '?? 0' untuk jaga-jaga jika datanya null, dianggap 0
                $stokSistem = $barang->stok_sekarang ?? 0; 
                
                $selisih = $jumlahFisik - $stokSistem;

                // Simpan detail per barang
                StokOpnameDetail::create([
                    'stok_opname_id' => $opname->id,
                    'barang_id' => $barang->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $jumlahFisik,
                    'selisih' => $selisih,
                ]);

                // --- PERBAIKAN 2: Update ke kolom 'stok_sekarang' ---
                $barang->update(['stok_sekarang' => $jumlahFisik]);
            }
        });

        return redirect()->route('gudang.dashboard', $gudang->id)
                         ->with('success', 'Stok Opname Selesai! Stok telah disesuaikan.');
    }

    // Menampilkan Daftar Riwayat
    public function index(Gudang $gudang)
    {
        // Ambil data opname urut dari yang paling baru
        $riwayats = StokOpname::where('gudang_id', $gudang->id)
                        ->latest('tanggal_opname')
                        ->latest('created_at') // backup sort jika tanggal sama
                        ->get();

        return view('opname.index', compact('gudang', 'riwayats'));
    }

    // Menampilkan Detail Satu Sesi Opname
    public function show(Gudang $gudang, StokOpname $stokOpname)
    {
        // Muat detail beserta data barangnya
        $stokOpname->load('details.barang');

        return view('opname.show', compact('gudang', 'stokOpname'));
    }
}