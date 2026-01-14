@extends('layout.app')

@section('title', 'Detail Stok Opname')

@section('content')
<div class="max-w-6xl mx-auto">
    
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('opname.index', $gudang->id) }}" class="w-10 h-10 flex items-center justify-center bg-white rounded-full shadow hover:bg-gray-100 text-gray-600 transition" title="Kembali ke Riwayat">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Stok Opname</h1>
            <p class="text-gray-500 text-sm">
                Tanggal: <span class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($stokOpname->tanggal_opname)->translatedFormat('d F Y') }}</span> 
                &bull; Pukul: {{ $stokOpname->created_at->format('H:i') }}
            </p>
        </div>
    </div>

    @if($stokOpname->catatan)
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 text-blue-800 rounded-r">
        <span class="font-bold">Catatan:</span> {{ $stokOpname->catatan }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="p-4 text-sm font-bold text-gray-600">Nama Barang</th>
                    <th class="p-4 text-sm font-bold text-gray-600 text-center">Stok Sistem</th>
                    <th class="p-4 text-sm font-bold text-gray-600 text-center">Stok Fisik</th>
                    <th class="p-4 text-sm font-bold text-gray-600 text-center">Selisih</th>
                    <th class="p-4 text-sm font-bold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($stokOpname->details as $d)
                <tr class="hover:bg-gray-50">
                    <td class="p-4">
                        <div class="font-bold text-gray-800">{{ $d->barang->nama_barang }}</div>
                        <div class="text-xs text-gray-400">{{ $d->barang->kategori ?? '-' }}</div>
                    </td>
                    
                    <td class="p-4 text-center text-gray-600 font-mono">
                        {{ $d->stok_sistem }}
                    </td>
                    
                    <td class="p-4 text-center font-bold font-mono">
                        {{ $d->stok_fisik }}
                    </td>

                    <td class="p-4 text-center font-bold">
                        @if($d->selisih > 0)
                            <span class="text-green-600">+{{ $d->selisih }}</span>
                        @elseif($d->selisih < 0)
                            <span class="text-red-600">{{ $d->selisih }}</span>
                        @else
                            <span class="text-gray-400">0</span>
                        @endif
                    </td>

                    <td class="p-4">
                        @if($d->selisih == 0)
                            <span class="inline-block px-2 py-1 text-xs font-bold text-green-700 bg-green-100 rounded-full">
                                <i class="fas fa-check"></i> Klop
                            </span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-bold text-red-700 bg-red-100 rounded-full">
                                <i class="fas fa-exclamation-triangle"></i> Selisih
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection