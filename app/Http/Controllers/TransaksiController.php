<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Gudang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    // Halaman Stok Masuk
    public function indexMasuk(Gudang $gudang)
    {
        // History 1 bulan terakhir untuk stok masuk
        $history = Transaksi::with('barang')
            ->whereHas('barang', fn($q) => $q->where('gudang_id', $gudang->id))
            ->where('jenis', 'masuk')
            ->where('tanggal', '>=', Carbon::now()->subMonth())
            ->latest()
            ->get();

        return view('transaksi.masuk', compact('gudang', 'history'));
    }

    // Halaman Stok Keluar
    public function indexKeluar(Gudang $gudang)
    {
        // History 1 bulan terakhir untuk stok keluar
        $history = Transaksi::with('barang')
            ->whereHas('barang', fn($q) => $q->where('gudang_id', $gudang->id))
            ->where('jenis', 'keluar')
            ->where('tanggal', '>=', Carbon::now()->subMonth())
            ->latest()
            ->get();

        return view('transaksi.keluar', compact('gudang', 'history'));
    }

    // 1. Menampilkan Halaman Stok Masuk (Update)
    public function masuk(Gudang $gudang)
    {
        // Ambil 10 transaksi 'masuk' terakhir dari gudang ini
        $history = Transaksi::whereHas('barang', function($q) use ($gudang) {
                $q->where('gudang_id', $gudang->id);
            })
            ->where('jenis', 'masuk')
            ->with('barang') // Agar nama barang bisa diambil
            ->latest()
            ->limit(10)
            ->get();

        return view('transaksi.masuk', compact('gudang', 'history'));
    }

    // 2. Menampilkan Halaman Stok Keluar (Update)
    public function keluar(Gudang $gudang)
    {
        // Ambil 10 transaksi 'keluar' terakhir dari gudang ini
        $history = Transaksi::whereHas('barang', function($q) use ($gudang) {
                $q->where('gudang_id', $gudang->id);
            })
            ->where('jenis', 'keluar')
            ->with('barang')
            ->latest()
            ->limit(10)
            ->get();

        return view('transaksi.keluar', compact('gudang', 'history'));
    }

    // Simpan Transaksi (Bisa dari Manual atau QR Batch)
    // Simpan Transaksi (Bisa dari Manual atau QR Batch)
    public function store(Request $request, Gudang $gudang)
    {
        $request->validate([
            'kode_barang' => 'required',
            'qty' => 'required|integer|min:1',
            'jenis' => 'required|in:masuk,keluar',
            'tanggal' => 'required|date'
        ]);

        // 1. Cari Barang
        $barang = Barang::where('gudang_id', $gudang->id)
            ->where(function($q) use ($request) {
                $q->where('kode_barang', $request->kode_barang)
                  ->orWhere('nama_barang', $request->kode_barang);
            })->first();

        if (!$barang) {
            return back()->withErrors(['msg' => 'Barang tidak ditemukan.']);
        }

        // 2. Ambil Stok Awal & Hitung Stok Akhir
        $stokAwal = $barang->stok_sekarang ?? 0;
        
        if ($request->jenis == 'masuk') {
            $stokAkhir = $stokAwal + $request->qty;
        } else {
            // Cek Stok Cukup (Khusus Keluar)
            if ($stokAwal < $request->qty) {
                return back()->withErrors([
                    'msg' => "Gagal! Stok tidak mencukupi. Sisa stok: {$stokAwal}, diminta: {$request->qty}"
                ])->withInput();
            }
            $stokAkhir = $stokAwal - $request->qty;
        }

        // 3. Simpan Transaksi dengan Snapshot 'stok_history'
        $transaksi = new Transaksi();
        $transaksi->barang_id = $barang->id;
        $transaksi->jenis = $request->jenis;
        $transaksi->tanggal = $request->tanggal;
        $transaksi->qty = $request->qty;
        $transaksi->satuan = $request->satuan ?? $barang->satuan;
        $transaksi->keterangan = $request->keterangan;
        
        // --- INI BAGIAN PENTINGNYA ---
        $transaksi->stok_history = $stokAkhir; 
        // -----------------------------
        
        $transaksi->save();

        // 4. Update Stok Master Barang ke angka terbaru
        $barang->stok_sekarang = $stokAkhir;
        $barang->save();

        return back()->with('success', 'Transaksi berhasil disimpan!');
    }

    // API Endpoint untuk menangani Scan QR (Batch)
   // API Endpoint untuk menangani Scan QR (Batch)
   public function handleScanQr(Request $request, Gudang $gudang)
    {
        $items = $request->input('items');
        $jenis = $request->input('jenis'); 

        // Gunakan DB Transaction agar aman
        try {
            DB::beginTransaction();

            foreach ($items as $item) {
                $barang = Barang::where('gudang_id', $gudang->id)
                                ->where('kode_barang', $item['kode'])->first();

                if ($barang) {
                    $stokAwal = $barang->stok_sekarang ?? 0;
                    
                    // Hitung Stok Akhir & Validasi
                    if ($jenis == 'masuk') {
                        $stokAkhir = $stokAwal + $item['qty'];
                    } else {
                        // Cek Stok Minus
                        if ($stokAwal < $item['qty']) {
                            DB::rollBack(); 
                            return response()->json([
                                'status' => 'error', 
                                'message' => "Gagal! Stok {$barang->nama_barang} (Kode: {$item['kode']}) tidak cukup. Sisa: {$stokAwal}"
                            ], 400); 
                        }
                        $stokAkhir = $stokAwal - $item['qty'];
                    }

                    // Simpan Transaksi dengan stok_history
                    Transaksi::create([
                        'barang_id' => $barang->id,
                        'jenis' => $jenis,
                        'tanggal' => now(),
                        'qty' => $item['qty'],
                        'satuan' => $barang->satuan,
                        'keterangan' => 'Scan QR Batch',
                        'stok_history' => $stokAkhir // <--- Tambahkan ini
                    ]);

                    // Update Master Barang
                    $barang->update(['stok_sekarang' => $stokAkhir]);
                }
            }

            DB::commit(); 
            return response()->json(['status' => 'success', 'message' => 'Data Scan berhasil disimpan']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}