@extends('layout.app')

@section('title', 'Stok Keluar')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-1 space-y-6">
        
        <div class="bg-white p-4 rounded shadow border-l-4 border-red-500">
            <h3 class="font-bold text-lg mb-2 text-red-700"><i class="fas fa-qrcode"></i> Scan Barang Keluar</h3>
            
            <div id="reader" class="w-full bg-black mb-2 hidden"></div>
            
            <button id="btnStartScan" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 shadow transition">
                <i class="fas fa-camera"></i> Mulai Scan Keluar
            </button>
            <button id="btnStopScan" class="w-full bg-gray-600 text-white py-2 rounded hover:bg-gray-700 hidden">
                Stop Kamera
            </button>

            <div id="scanResultBox" class="mt-4 hidden">
                <h4 class="text-sm font-bold border-b pb-1 text-red-600">Daftar Barang Keluar:</h4>
                <ul id="scannedList" class="text-sm mt-2 max-h-40 overflow-y-auto space-y-1">
                    </ul>
                <button onclick="submitBatchScan()" class="mt-3 w-full bg-red-800 text-white py-2 rounded font-bold text-sm hover:bg-red-900 animate-pulse">
                    KONFIRMASI KELUAR
                </button>
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-4 text-gray-700">Input Manual Keluar</h3>
            <form action="{{ route('transaksi.store', $gudang->id) }}" method="POST">
                @csrf
                <input type="hidden" name="jenis" value="keluar">
                
                <div class="mb-2">
                    <label class="text-sm font-semibold">Kode / Nama Barang</label>
                    <input type="text" name="kode_barang" class="w-full border p-2 rounded focus:ring-red-500 focus:border-red-500" required placeholder="Scan atau ketik manual...">
                </div>
                <div class="flex gap-2 mb-2">
                    <div class="flex-1">
                        <label class="text-sm font-semibold">Tanggal Keluar</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded">
                    </div>
                    <div class="w-24">
                        <label class="text-sm font-semibold">Qty</label>
                        <input type="number" name="qty" value="1" min="1" class="w-full border p-2 rounded">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-sm font-semibold">Keterangan / Tujuan</label>
                    <textarea name="keterangan" class="w-full border p-2 rounded h-20" placeholder="Contoh: Rusak, Terjual, dll"></textarea>
                </div>
                <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded hover:bg-slate-900">
                    <i class="fas fa-minus-circle"></i> Keluarkan Barang
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded shadow border-t-4 border-red-500">
            <h3 class="font-bold text-lg mb-4 text-red-700">Riwayat Barang Keluar (30 Hari Terakhir)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-red-50 text-red-900">
                        <tr>
                            <th class="p-3 rounded-tl">Tgl</th>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Barang</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3 rounded-tr">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $h)
                        <tr class="border-b hover:bg-red-50 transition">
                            <td class="p-3">{{ \Carbon\Carbon::parse($h->tanggal)->format('d/m/Y') }}</td>
                            <td class="p-3 font-mono text-xs text-gray-500">{{ $h->barang->kode_barang }}</td>
                            <td class="p-3 font-bold text-gray-700">{{ $h->barang->nama_barang }}</td>
                            <td class="p-3 text-red-600 font-bold text-base">
                                -{{ $h->qty }} <span class="text-xs font-normal text-gray-500">{{ $h->satuan }}</span>
                            </td>
                            <td class="p-3 text-gray-500 text-xs italic">{{ $h->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-400">Belum ada barang keluar bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // LOGIKA SCANNER QR (Khusus Stok Keluar)
    let scannedData = {}; 
    let html5QrcodeScanner = null;

    document.getElementById('btnStartScan').addEventListener('click', function() {
        document.getElementById('reader').classList.remove('hidden');
        document.getElementById('btnStartScan').classList.add('hidden');
        document.getElementById('btnStopScan').classList.remove('hidden');
        document.getElementById('scanResultBox').classList.remove('hidden');

        html5QrcodeScanner = new Html5Qrcode("reader");
        html5QrcodeScanner.start(
            { facingMode: "environment" }, 
            { fps: 10, qrbox: 250 },
            onScanSuccess
        );
    });

    document.getElementById('btnStopScan').addEventListener('click', function() {
        if(html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(() => {
                document.getElementById('reader').classList.add('hidden');
                document.getElementById('btnStartScan').classList.remove('hidden');
                document.getElementById('btnStopScan').classList.add('hidden');
            });
        }
    });

    function onScanSuccess(decodedText, decodedResult) {
        // Logika hitung di browser dulu
        if (scannedData[decodedText]) {
            scannedData[decodedText]++;
        } else {
            scannedData[decodedText] = 1;
        }
        renderScannedList();
    }

    function renderScannedList() {
        const list = document.getElementById('scannedList');
        list.innerHTML = '';
        
        for (const [kode, qty] of Object.entries(scannedData)) {
            let li = document.createElement('li');
            li.className = 'flex justify-between bg-red-50 p-2 rounded border border-red-100';
            li.innerHTML = `<span>${kode}</span> <span class="font-bold text-red-600">-${qty}</span>`;
            list.appendChild(li);
        }
    }

    function submitBatchScan() {
        if (Object.keys(scannedData).length === 0) return alert('Belum ada barang discan!');

        let itemsToSend = [];
        for (const [kode, qty] of Object.entries(scannedData)) {
            itemsToSend.push({ kode: kode, qty: qty });
        }

        // Kirim via Fetch API
        fetch('/gudangs/{{ $gudang->id }}/api/scan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                items: itemsToSend,
                jenis: 'keluar'
            })
        })
        .then(async response => {
            const data = await response.json(); // Baca JSON response
            
            if (!response.ok) {
                // Jika server mengirim error (stok kurang), lempar ke catch
                throw new Error(data.message || 'Terjadi kesalahan');
            }
            return data;
        })
        .then(data => {
            alert('Sukses! Stok telah dikeluarkan.');
            window.location.reload(); 
        })
        .catch(error => {
            // Tampilkan pesan error spesifik (Misal: Stok Barang X tidak cukup)
            alert(error.message); 
            console.error('Error:', error);
        });
    }
</script>
@endsection