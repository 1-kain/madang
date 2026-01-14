@extends('layout.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Dashboard: {{ $gudang->nama_gudang }}</h2>
        <p class="text-gray-600">
            Selamat datang di panel pengelolaan gudang. 
            <br>
            (Area ini siap untuk diisi dengan grafik atau ringkasan stok nanti).
        </p>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-gray-700">
            <i class="fas fa-history mr-2 text-blue-500"></i> Riwayat Aktivitas Terbaru
        </h3>
        <span class="text-xs text-gray-400">Gabungan Masuk & Keluar</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-semibold">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Barang</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Qty</th>
                    <th class="px-6 py-4 text-center">Stok Terkini</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($history as $h)
                <tr class="hover:bg-gray-50 transition">
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-700">{{ $h->tanggal->format('d M Y') }}</span>
                            <span class="text-xs text-gray-400">{{ $h->tanggal->format('H:i') }} WIB</span>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded bg-gray-200 flex-shrink-0 overflow-hidden border">
                                @if($h->barang->foto)
                                    <img src="{{ asset('storage/' . $h->barang->foto) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div>
                                <div class="font-bold text-gray-800 text-sm">{{ $h->barang->nama_barang }}</div>
                                <span class="inline-block bg-blue-50 text-blue-600 text-[10px] px-2 py-0.5 rounded-full border border-blue-100 mt-1">
                                    {{ $h->barang->kategori ?? 'Umum' }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        @if($h->jenis == 'masuk')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                <i class="fas fa-arrow-down"></i> Masuk
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                <i class="fas fa-arrow-up"></i> Keluar
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center font-bold text-gray-700">
                        {{ $h->qty }}
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="font-mono bg-gray-100 text-gray-800 px-2 py-1 rounded border border-gray-300 text-xs">
                            {{ $h->barang->stok }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-clipboard-list text-3xl mb-2 text-gray-300"></i>
                            <p>Belum ada riwayat transaksi</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection