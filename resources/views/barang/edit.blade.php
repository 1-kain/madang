@extends('layout.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded shadow-lg">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-2xl font-bold text-gray-800">Edit Barang</h2>
        <a href="{{ route('barangs.index', $gudang->id) }}" class="text-gray-500 hover:text-gray-800">
            <i class="fas fa-times"></i> Batal
        </a>
    </div>

    <form action="{{ route('barangs.update', [$gudang->id, $barang->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') 
        
        <div class="flex gap-6 mb-6 bg-gray-50 p-4 rounded border border-gray-200">
            <div class="w-1/4">
                <label class="block text-sm font-bold mb-2 text-gray-600">Foto Saat Ini</label>
                @if($barang->gambar)
                    <img src="{{ asset('storage/' . $barang->gambar) }}" class="w-full h-32 object-cover rounded shadow border bg-white">
                @else
                    <div class="w-full h-32 bg-white rounded flex items-center justify-center text-gray-400 border border-dashed">
                        <div class="text-center">
                            <i class="fas fa-image text-2xl mb-1"></i>
                            <span class="text-xs block">Tidak ada foto</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="w-3/4 flex flex-col justify-center">
                <label class="block text-sm font-bold mb-2 text-gray-700">Ganti Foto Produk</label>
                <input type="file" name="gambar" class="w-full border border-gray-300 p-2 rounded bg-white text-sm focus:outline-none focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle"></i> Biarkan kosong jika tidak ingin mengubah foto. Format: JPG/PNG, Max 2MB.
                </p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-bold mb-1">Kode Barang</label>
                <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" class="w-full border p-2 rounded bg-gray-50" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="w-full border p-2 rounded" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-bold mb-1 text-gray-600">
                Stok Awal <span class="text-xs font-normal text-red-500">(Permanen - Tidak bisa diedit)</span>
            </label>
            <div class="flex items-center">
                <span class="bg-gray-200 border border-r-0 border-gray-300 p-2 rounded-l text-gray-500">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="number" 
                       value="{{ $barang->stok_awal }}" 
                       class="w-full border border-gray-300 p-2 rounded-r bg-gray-200 text-gray-500 cursor-not-allowed focus:outline-none" 
                       readonly>
            </div>
            <p class="text-xs text-gray-400 mt-1">Jika ingin menambah/mengurangi stok, gunakan menu Stok Masuk/Keluar.</p>
        </div>

        @if($kategoris->count() > 0)
        <div class="bg-blue-50 p-4 rounded mb-6 border border-blue-100">
            <h4 class="text-sm font-bold text-blue-800 mb-3">Atribut Tambahan</h4>
            <div class="grid grid-cols-2 gap-4">
                @foreach($kategoris as $k)
                <div>
                    <label class="text-xs font-bold text-gray-600 uppercase">{{ $k->nama_kategori }}</label>
                    <input type="text" 
                           name="attr[{{ $k->nama_kategori }}]" 
                           value="{{ $barang->atribut_tambahan[$k->nama_kategori] ?? '' }}" 
                           class="w-full border p-2 rounded text-sm mt-1">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="mb-6">
            <label class="block text-sm font-bold mb-1">Keterangan</label>
            <textarea name="keterangan" class="w-full border p-2 rounded h-24">{{ old('keterangan', $barang->keterangan) }}</textarea>
        </div>

        <div class="mb-6 flex items-center bg-gray-50 p-3 rounded">
            <input type="checkbox" name="has_qr" id="has_qr" class="mr-2 h-5 w-5" {{ $barang->has_qr ? 'checked' : '' }}>
            <label for="has_qr" class="text-sm font-semibold text-gray-700">Aktifkan QR Code untuk barang ini?</label>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('barangs.index', $gudang->id) }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold shadow-lg">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection