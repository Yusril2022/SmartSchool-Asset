<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Habis</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md text-center space-y-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">
            <div class="text-6xl mb-4">📭</div>
            <h1 class="text-xl font-bold text-gray-800">Stok Habis</h1>
            <p class="text-gray-500 text-sm mt-2">
                Stok <span class="font-medium text-gray-800">{{ $item->nama_barang }}</span>
                sedang habis. Silakan hubungi admin untuk penambahan stok.
            </p>
        </div>
    </div>

</body>

</html>