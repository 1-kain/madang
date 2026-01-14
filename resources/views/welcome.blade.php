<!DOCTYPE html>
<html lang="id">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>MADANG - Welcome</title>
</head>
<body class="bg-slate-900 h-screen flex flex-col justify-center items-center text-white">
    
    <div class="text-center animate-bounce">
        <h1 class="text-6xl font-extrabold text-yellow-400 tracking-widest mb-4">MADANG</h1>
        <p class="text-xl text-slate-300">Manajemen Gudang Terpadu</p>
    </div>

    <div class="mt-12 space-y-4">
        <a href="{{ route('gudangs.index') }}" class="block px-8 py-3 bg-yellow-500 text-slate-900 font-bold rounded-full hover:bg-yellow-400 transition transform hover:scale-105 shadow-lg">
            Mulai Aplikasi
        </a>
    </div>

    <div class="fixed bottom-4 text-xs text-slate-600">
        &copy; 2024 Madang WMS
    </div>
</body>
</html>