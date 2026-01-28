@extends('layout.app')

@section('title', 'Pilih Gudang')

@section('content')

    @if($errors->any())
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm relative" role="alert">
        <strong class="font-bold">Terjadi Kesalahan!</strong>
        <span class="block sm:inline">{{ $errors->first('msg') ?? 'Terjadi kesalahan sistem.' }}</span>
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h3 class="text-lg font-bold mb-4">Buat Gudang Baru</h3>
            <form action="{{ route('gudangs.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="nama_gudang" placeholder="Nama Gudang (misal: Gudang Elektronik)" class="flex-1 border p-2 rounded" required>
                <input type="text" name="lokasi" placeholder="Alamat (Opsional)" class="flex-1 border p-2 rounded">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Buat</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($gudangs as $g)
            <div class="relative group bg-white rounded-lg shadow hover:shadow-xl transition duration-300 border-2 border-transparent hover:border-yellow-400 overflow-hidden">
                
                <a href="{{ route('gudang.dashboard', $g->id) }}" class="block p-6 h-full">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                            <i class="fas fa-warehouse text-xl"></i>
                        </div>
                        <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2 py-1 rounded-full">
                            {{ $g->barangs_count ?? $g->barangs->count() }} Item
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $g->nama_gudang }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $g->lokasi ?? 'Lokasi belum diatur' }}</p>

                    <div class="text-xs text-gray-400 border-t pt-3 mt-auto">
                        <i class="fas fa-clock mr-1"></i> Dibuat: {{ $g->created_at->diffForHumans() }}
                    </div>
                </a>

                <div class="absolute top-4 right-4 z-20 flex gap-1">
                    <button onclick="openEditModal({{ $g->id }}, '{{ $g->nama_gudang }}', '{{ $g->lokasi }}')" 
                            class="bg-white text-gray-400 hover:text-yellow-500 p-2 rounded-full shadow-sm hover:shadow-md transition border border-transparent hover:border-yellow-200" 
                            title="Edit Gudang">
                        <i class="fas fa-pencil-alt"></i>
                    </button>

                    <form action="{{ route('gudangs.destroy', $g->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Gudang ini akan dihapus permanen. Lanjutkan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-white text-gray-400 hover:text-red-600 p-2 rounded-full shadow-sm hover:shadow-md transition border border-transparent hover:border-red-200" title="Hapus Gudang">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    <div id="modalEditGudang" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100]">
        <div class="bg-white w-full max-w-md p-6 rounded shadow-lg relative animate-fade-in-down">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Edit Data Gudang</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>

            <form id="formEditGudang" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">Nama Gudang</label>
                    <input type="text" id="edit_nama_gudang" name="nama_gudang" class="w-full border p-2 rounded" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">Lokasi / Alamat</label>
                    <input type="text" id="edit_lokasi" name="lokasi" class="w-full border p-2 rounded">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 shadow">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, nama, lokasi) {
            // 1. Isi input form dengan data yang diklik
            document.getElementById('edit_nama_gudang').value = nama;
            document.getElementById('edit_lokasi').value = lokasi;

            // 2. Set action URL form agar sesuai ID gudang
            // Kita gunakan route placeholder '0' lalu replace dengan ID asli
            let url = "{{ route('gudangs.update', 0) }}";
            url = url.replace('/0', '/' + id); 
            document.getElementById('formEditGudang').action = url;

            // 3. Tampilkan modal
            document.getElementById('modalEditGudang').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('modalEditGudang').classList.add('hidden');
        }

        // Tutup modal jika klik di luar area putih
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modalEditGudang');
            if (event.target == modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
@endsection