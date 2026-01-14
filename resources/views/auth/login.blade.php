<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex justify-center items-center">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-slate-800">Sistem Gudang</h1>
            <p class="text-gray-500 text-sm">Silakan login untuk mengelola stok</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" 
                       class="w-full border p-3 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                       placeholder="admin@example.com" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" 
                       class="w-full border p-3 rounded focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" 
                       placeholder="********" required>
            </div>

            <button type="submit" class="w-full bg-slate-800 text-white font-bold py-3 rounded hover:bg-slate-900 transition duration-300">
                MASUK
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Manajemen Stok</p>
        </div>
    </div>

</body>
</html>