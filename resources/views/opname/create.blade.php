@extends('layout.app')

@section('title', 'Stok Opname - ' . $gudang->nama_gudang)

@section('content')

<div class="bg-white rounded-lg shadow-lg p-6">
    <div class="border-b pb-4 mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Formulir Stok Opname</h2>
        <p class="text-gray-500">Silakan hitung fisik barang dan masukkan jumlah nyatanya di kolom "Fisik".</p>
    </div>

    <form action="{{ route('opname.store', $gudang->id) }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block font-bold mb-1">Tanggal Opname</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full border p-2 rounded" required>
            </div>
            <div>
                <label class="block font-bold mb-1">Catatan (Opsional)</label>
                <input type="text" name="catatan" class="w-full border p-2 rounded" placeholder="Contoh: Audit Tahunan">
            </div>
        </div>

        <div class="overflow-x-auto mb-6 border rounded">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-3">Nama Barang</th>
                        <th class="p-3 w-32 text-center bg-blue-50">Stok Sistem</th>
                        <th class="p-3 w-40 text-center bg-green-50">Stok Fisik (Input)</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($barangs as $b)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <div class="font-bold">{{ $b->nama_barang }}</div>
                            <div class="text-xs text-gray-500">{{ $b->kategori ?? '-' }}</div>
                        </td>
                        
                        <td class="p-3 text-center font-mono text-blue-600 font-bold bg-blue-50">
                            {{ $b->stok }}
                        </td>

                        <td class="p-3 bg-green-50">
                            <input type="number" 
                                   name="fisik[{{ $b->id }}]" 
                                   value="{{ $b->stok }}" 
                                   class="w-full border border-green-300 p-2 rounded text-center font-bold focus:ring-2 focus:ring-green-500 outline-none" 
                                   required>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('gudang.dashboard', $gudang->id) }}" class="px-6 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</a>
            <button type="submit" onclick="return confirm('Apakah Anda yakin? Stok sistem akan diperbarui sesuai input fisik.')" class="px-6 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-700">
                Selesai & Sesuaikan Stok
            </button>
        </div>
    </form>
</div>

@endsection