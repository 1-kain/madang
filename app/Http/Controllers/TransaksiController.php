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

        // === LOGIKA BARU: CEGAH STOK MINUS ===
        if ($request->jenis == 'keluar') {
            if ($barang->stok_sekarang < $request->qty) {
                return back()->withErrors([
                    'msg' => "Gagal! Stok tidak mencukupi. Sisa stok: {$barang->stok_sekarang}, diminta: {$request->qty}"
                ])->withInput();
            }
        }
        // =====================================

        // Simpan Transaksi
        $transaksi = new Transaksi();
        $transaksi->barang_id = $barang->id;
        $transaksi->jenis = $request->jenis;
        $transaksi->tanggal = $request->tanggal;
        $transaksi->qty = $request->qty;
        $transaksi->satuan = $request->satuan ?? $barang->satuan;
        $transaksi->keterangan = $request->keterangan;
        $transaksi->save();

        // Update Stok
        if ($request->jenis == 'masuk') {
            $barang->increment('stok_sekarang', $request->qty);
        } else {
            $barang->decrement('stok_sekarang', $request->qty);
        }

        return back()->with('success', 'Transaksi berhasil disimpan!');
    }

    // API Endpoint untuk menangani Scan QR (Batch)
   public function handleScanQr(Request $request, Gudang $gudang)
    {
        $items = $request->input('items');
        $jenis = $request->input('jenis'); 

        // Gunakan DB Transaction agar aman (Semua sukses atau batal semua)
        try {
            DB::beginTransaction();

            foreach ($items as $item) {
                $barang = Barang::where('gudang_id', $gudang->id)
                                ->where('kode_barang', $item['kode'])->first();

                if ($barang) {
                    
                    // === LOGIKA BARU: CEGAH STOK MINUS DI QR ===
                    if ($jenis == 'keluar' && $barang->stok_sekarang < $item['qty']) {
                        // Batalkan semua proses jika ada 1 barang yang kurang
                        DB::rollBack(); 
                        return response()->json([
                            'status' => 'error', 
                            'message' => "Gagal! Stok {$barang->nama_barang} (Kode: {$item['kode']}) tidak cukup. Sisa: {$barang->stok_sekarang}"
                        ], 400); // 400 Bad Request
                    }
                    // ===========================================

                    Transaksi::create([
                        'barang_id' => $barang->id,
                        'jenis' => $jenis,
                        'tanggal' => now(),
                        'qty' => $item['qty'],
                        'satuan' => $barang->satuan,
                        'keterangan' => 'Scan QR Batch'
                    ]);

                    if ($jenis == 'masuk') {
                        $barang->increment('stok_sekarang', $item['qty']);
                    } else {
                        $barang->decrement('stok_sekarang', $item['qty']);
                    }
                }
            }

            DB::commit(); // Simpan permanen jika tidak ada error
            return response()->json(['status' => 'success', 'message' => 'Data Scan berhasil disimpan']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}