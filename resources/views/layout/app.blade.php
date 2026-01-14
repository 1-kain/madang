<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MADANG WMS</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        @if(isset($gudang))
        <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl transition-all duration-300 z-20">
            <div class="h-16 flex flex-col items-center justify-center border-b border-slate-700 bg-slate-950">
                <h1 class="text-xl font-extrabold tracking-wider text-yellow-400">
                    <i class="fas fa-cubes mr-2"></i>MADANG
                </h1>
                <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ $gudang->nama_gudang }}</p>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                
                <a href="{{ route('gudang.dashboard', $gudang->id) }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('gudang.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-home w-5 text-center"></i> 
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('barangs.index', $gudang->id) }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('barangs.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-boxes w-5 text-center"></i>
                    <span class="font-medium">Data Barang</span>
                </a>

                <div class="pt-4 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Transaksi</div>

                <a href="{{ route('transaksi.masuk', $gudang->id) }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('transaksi.masuk') ? 'bg-emerald-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-arrow-down w-5 text-center text-emerald-400 {{ request()->routeIs('transaksi.masuk') ? 'text-white' : '' }}"></i>
                    <span class="font-medium">Stok Masuk</span>
                </a>

                <a href="{{ route('transaksi.keluar', $gudang->id) }}" 
                   class="flex items-center gap-3 p-3 rounded-lg transition-colors {{ request()->routeIs('transaksi.keluar') ? 'bg-rose-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="fas fa-arrow-up w-5 text-center text-rose-400 {{ request()->routeIs('transaksi.keluar') ? 'text-white' : '' }}"></i>
                    <span class="font-medium">Stok Keluar</span>
                </a>

                <div class="pt-4 pb-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider">System</div>

                <a href="{{ route('gudangs.index') }}" class="flex items-center gap-3 p-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-yellow-400 transition-colors">
                    <i class="fas fa-exchange-alt w-5 text-center"></i>
                    <span class="font-medium">Ganti Gudang</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700 text-xs text-center text-slate-500">
                &copy; {{ date('Y') }} Madang WMS
            </div>
        </aside>
        @endif

        <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
            
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 z-10 sticky top-0">
                <div class="flex items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        @yield('title', 'Aplikasi Gudang')
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-gray-700">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500">Admin</p>
                    </div>
                    
                    <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-600 transition p-2" title="Logout">
                            <i class="fas fa-sign-out-alt text-lg"></i>
                        </button>
                    </form>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto bg-gray-50 p-6">
                
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between animate-fade-in-down">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-xl mr-3"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">&times;</button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm animate-fade-in-down">
                        <div class="font-bold flex items-center mb-1">
                            <i class="fas fa-exclamation-circle mr-2"></i> Terjadi Kesalahan
                        </div>
                        <ul class="list-disc list-inside text-sm ml-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
                
                <div class="h-10"></div>
            </div>
        </main>
    </div>

    @yield('scripts')
</body>
</html>