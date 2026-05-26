<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="text-center space-y-6 max-w-md">

        <div class="text-8xl font-bold text-red-500">403</div>

        <div>
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">
                Akses Ditolak
            </h1>
            <p class="text-gray-500 text-sm">
                Kamu tidak memiliki izin untuk mengakses halaman ini.
            </p>
        </div>

        <div class="flex justify-center gap-3">
            <a href="{{ url()->previous() }}"
                class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                ← Kembali
            </a>
            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition text-sm">
                Dashboard
            </a>
        </div>

        <p class="text-xs text-gray-400">Smart School Asset Management System</p>

    </div>

</body>

</html>