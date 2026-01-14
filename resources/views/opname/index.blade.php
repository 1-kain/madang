@extends('layout.app')

@section('title', 'Riwayat Stok Opname')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-history text-blue-600 mr-2"></i> Riwayat Stok Opname
        </h2>
        <a href="{{ route('gudang.dashboard', $gudang->id) }}" class="text-gray-600 hover:text-blue-600 font-medium transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="grid gap-4">
        @forelse($riwayats as $r)
        <a href="{{ route('opname.show', [$gudang->id, $r->id]) }}" class="block bg-white p-6 rounded-lg shadow-sm border border-gray-200 hover:shadow-md hover:border-blue-400 transition group">
            <div class="flex justify-between items-center">
                
                <div>
                    <div class="text-xs text-gray-400 font-bold uppercase mb-1">
                        Sesi Opname #{{ $r->id }}
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600">
                        {{ \Carbon\Carbon::parse($r->tanggal_opname)->translatedFormat('l, d F Y') }}
                    </h3>
                    @if($r->catatan)
                        <p class="text-gray-500 text-sm mt-1">"{{ $r->catatan }}"</p>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="block text-xs text-gray-400">Dibuat</span>
                        <span class="text-sm font-medium text-gray-600">{{ $r->created_at->format('H:i') }} WIB</span>
                    </div>
                    <div class="bg-blue-50 text-blue-600 w-10 h-10 rounded-full flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-12 bg-white rounded-lg border border-dashed border-gray-300">
            <i class="fas fa-clipboard text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Belum ada riwayat Stok Opname.</p>
            <a href="{{ route('opname.create', $gudang->id) }}" class="text-blue-600 font-bold hover:underline mt-2 inline-block">Mulai Opname Baru</a>
        </div>
        @endforelse
    </div>
</div>
@endsection