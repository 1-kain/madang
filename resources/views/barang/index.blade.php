@extends('layout.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Data Barang</h2>
    <div class="flex gap-2">
        <button onclick="openModal('modalKategori')" class="bg-purple-600 text-white px-4 py-2 rounded shadow hover:bg-purple-700 transition">
            <i class="fas fa-plus mr-2"></i> Tambah Kategori
        </button>
        <button onclick="openModal('modalBarang')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
            + Tambah Barang
        </button>
    </div>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-100 text-slate-600 uppercase text-sm font-bold">
            <tr>
                <th class="p-3 border-b text-center">Foto</th>
                <th class="p-3 border-b">Kode</th>
                <th class="p-3 border-b">Nama Barang</th>
                <th class="p-3 border-b">Stok</th>
                <th class="p-3 border-b">Satuan</th>
                
                @foreach($kategoris as $k)
                <th class="p-3 border-b text-center min-w-[120px] bg-slate-100 border-l border-slate-200 group relative">
                    <div class="flex justify-between items-center gap-2">
                        <span class="font-bold text-slate-700 uppercase text-xs w-full">
                            {{ $k->nama_kategori }}
                        </span>
                        <form action="{{ route('kategori-atribut.destroy', [$gudang->id, $k->id]) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus kolom {{ $k->nama_kategori }}? Data atribut ini pada semua barang akan ikut hilang/tersembunyi.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-600 transition duration-200" title="Hapus Kolom Ini">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </form>
                    </div>
                </th>
                @endforeach
                
                <th class="p-3 border-b">QR Code</th>
                <th class="p-3 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @foreach($barangs as $item)
            <tr class="hover:bg-gray-50 border-b">
                <td class="p-3 text-center">
                    @if($item->gambar)
                        <img src="{{ asset('storage/' . $item->gambar) }}" class="h-12 w-12 object-cover rounded shadow mx-auto" alt="Produk">
                    @else
                        <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center text-gray-400 mx-auto">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                </td>
                <td class="p-3 font-mono font-bold">{{ $item->kode_barang }}</td>
                <td class="p-3">{{ $item->nama_barang }}</td>
                <td class="p-3">
                    <span class="px-2 py-1 rounded {{ $item->stok_sekarang > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $item->stok_sekarang }}
                    </span>
                </td>
                <td class="p-3">{{ $item->satuan }}</td>
                
                @foreach($kategoris as $k)
                    <td class="p-3 text-gray-500">
                        {{ $item->atribut_tambahan[$k->nama_kategori] ?? '-' }}
                    </td>
                @endforeach

                <td class="p-3">
                    @if($item->has_qr)
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data={{ $item->kode_barang }}" alt="QR">
                    @else
                        <span class="text-gray-400 text-xs">No QR</span>
                    @endif
                </td>
                <td class="p-3 flex gap-2">
                    <a href="{{ route('barangs.edit', [$gudang->id, $item->id]) }}" class="text-white bg-yellow-500 hover:bg-yellow-600 px-2 py-1 rounded text-xs shadow">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('barangs.destroy', [$gudang->id, $item->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini? Data stok dan history juga akan terhapus!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-2 py-1 rounded text-xs shadow">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div id="modalBarang" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100]">
    <div class="bg-white w-full max-w-lg p-6 rounded shadow-lg max-h-screen overflow-y-auto relative animate-fade-in-down">
        
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Tambah Data Barang</h3>
            <button onclick="closeModal('modalBarang')" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <form action="{{ route('barangs.store', $gudang->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Foto Produk</label>
                <input type="file" name="gambar" class="w-full text-sm border p-2 rounded bg-gray-50">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks: 2MB.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Kode Barang</label>
                    <div class="flex">
                        <input type="text" id="kodeBarangInput" name="kode_barang" class="w-full border p-2 rounded-l" required>
                        <button type="button" onclick="document.getElementById('kodeBarangInput').value = 'BRG-' + Math.floor(Math.random() * 10000)" class="bg-gray-200 p-2 rounded-r text-xs font-bold hover:bg-gray-300">Gen</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Barang</label>
                    <input type="text" name="nama_barang" class="w-full border p-2 rounded" required>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold mb-1">Stok Awal</label>
                    <input type="number" name="stok_awal" class="w-full border p-2 rounded" value="0">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Satuan</label>
                    <input type="text" name="satuan" class="w-full border p-2 rounded" placeholder="Pcs/Unit/Kg" required>
                </div>
            </div>

            @if($kategoris->count() > 0)
            <div class="bg-yellow-50 p-3 rounded mb-4 border border-yellow-200">
                <h4 class="text-sm font-bold text-yellow-800 mb-2">Atribut Tambahan</h4>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($kategoris as $k)
                    <div>
                        <label class="text-xs text-gray-600 uppercase font-bold">{{ $k->nama_kategori }}</label>
                        <input type="text" name="attr[{{ $k->nama_kategori }}]" class="w-full border p-1 rounded text-sm bg-white">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-4 flex items-center">
                <input type="checkbox" name="has_qr" id="has_qr" class="mr-2 h-4 w-4">
                <label for="has_qr" class="text-sm">Generate QR Code untuk barang ini?</label>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeModal('modalBarang')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="modalKategori" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100]">
    <div class="bg-white w-96 p-6 rounded shadow-lg relative animate-fade-in-down">
        <h3 class="text-lg font-bold mb-4">Tambah Kategori Baru</h3>
        
        <form action="{{ route('kategori-atribut.store', $gudang->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                <input type="text" name="nama_kategori" class="w-full border p-2 rounded focus:ring-2 focus:ring-purple-500 outline-none" placeholder="Contoh: Warna, Ukuran" required>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalKategori')" class="text-gray-500 hover:text-gray-700 px-4 py-2">
                    Batal
                </button>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 shadow">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fungsi untuk membuka modal
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    // Fungsi untuk menutup modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Menutup modal jika area gelap di luar kotak putih diklik
    // Kita gunakan addEventListener agar tidak bentrok dengan script lain
    window.addEventListener('click', function(event) {
        const modalKategori = document.getElementById('modalKategori');
        const modalBarang = document.getElementById('modalBarang');

        if (event.target == modalKategori) {
            modalKategori.classList.add('hidden');
        }
        if (event.target == modalBarang) {
            modalBarang.classList.add('hidden');
        }
    });

    // Menutup modal dengan tombol ESC
    window.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            closeModal('modalKategori');
            closeModal('modalBarang');
        }
    });
</script>
@endsection