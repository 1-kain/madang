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
            <input type="text" name="nama_gudang" placeholder="Nama Gudang (misal: Gudang Elektronik)" class="flex-1 border p-2 rounded">
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

        <div class="absolute top-4 right-4 z-10">
            <form action="{{ route('gudangs.destroy', $g->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Gudang ini akan dihapus permanen. Lanjutkan?');">
                @csrf
                @method('DELETE')
                
                <button type="submit" class="bg-white text-gray-400 hover:text-red-600 p-2 rounded-full shadow-sm hover:shadow-md transition" title="Hapus Gudang">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>

    </div>
@endforeach
    </div>
</div>
@endsection