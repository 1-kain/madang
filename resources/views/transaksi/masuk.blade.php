@extends('layout.app')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-1 space-y-6">
        
        <div class="bg-white p-4 rounded shadow border-l-4 border-blue-500">
            <h3 class="font-bold text-lg mb-2"><i class="fas fa-qrcode"></i> Scan QR Masuk</h3>
            
            <div id="reader" class="w-full bg-black mb-2 hidden"></div>
            
            <button id="btnStartScan" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                Mulai Kamera
            </button>
            <button id="btnStopScan" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 hidden">
                Stop Kamera
            </button>

            <div id="scanResultBox" class="mt-4 hidden">
                <h4 class="text-sm font-bold border-b pb-1">Barang Ter-scan:</h4>
                <ul id="scannedList" class="text-sm mt-2 max-h-40 overflow-y-auto space-y-1">
                    </ul>
                <button onclick="submitBatchScan()" class="mt-3 w-full bg-green-600 text-white py-2 rounded font-bold text-sm">
                    SIMPAN SEMUA STOK
                </button>
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-bold text-lg mb-4">Input Manual</h3>
            <form action="{{ route('transaksi.store', $gudang->id) }}" method="POST">
                @csrf
                <input type="hidden" name="jenis" value="masuk">
                
                <div class="mb-2">
                    <label class="text-sm">Kode / Nama Barang</label>
                    <input type="text" name="kode_barang" class="w-full border p-2 rounded" required>
                </div>
                <div class="flex gap-2 mb-2">
                    <div class="flex-1">
                        <label class="text-sm">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded">
                    </div>
                    <div class="w-24">
                        <label class="text-sm">Qty</label>
                        <input type="number" name="qty" value="1" class="w-full border p-2 rounded">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-sm">Keterangan</label>
                    <textarea name="keterangan" class="w-full border p-2 rounded h-20"></textarea>
                </div>
                <button type="submit" class="w-full bg-slate-800 text-white py-2 rounded">Tambah Manual</button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-bold text-lg mb-4">Riwayat Stok Masuk (30 Hari Terakhir)</h3>
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">Tgl</th>
                        <th class="p-2">Kode</th>
                        <th class="p-2">Barang</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2">Ket</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($history as $h)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2">{{ $h->tanggal }}</td>
                        <td class="p-2 font-mono text-xs">{{ $h->barang->kode_barang }}</td>
                        <td class="p-2 font-bold">{{ $h->barang->nama_barang }}</td>
                        <td class="p-2 text-green-600 font-bold">+{{ $h->qty }} {{ $h->satuan }}</td>
                        <td class="p-2 text-gray-500 text-xs">{{ $h->keterangan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // LOGIKA SCANNER QR
    let scannedData = {}; // Object untuk menyimpan batch scan: {'BRG-001': 5, 'BRG-002': 1}
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
        // Efek Suara (Opsional)
        // let audio = new Audio('beep.mp3'); audio.play();

        // Tambah Qty jika sudah ada, atau buat baru
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
            li.className = 'flex justify-between bg-gray-100 p-2 rounded';
            li.innerHTML = `<span>${kode}</span> <span class="font-bold text-blue-600">x${qty}</span>`;
            list.appendChild(li);
        }
    }

    function submitBatchScan() {
        if (Object.keys(scannedData).length === 0) return alert('Belum ada barang discan!');

        // Ubah object ke format array untuk dikirim ke API
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
                jenis: 'masuk' // hardcode jenis transaksi
            })
        })
        .then(response => response.json())
        .then(data => {
            alert('Sukses! Semua stok QR telah masuk.');
            window.location.reload(); // Refresh halaman
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection