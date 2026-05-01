<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berhasil — Smart School Assets</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md text-center space-y-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">

            <div class="text-6xl mb-4">✅</div>

            <h1 class="text-2xl font-bold text-gray-800">Berhasil!</h1>

            <p class="text-gray-500 mt-2 text-sm">
                Pengambilan barang telah tercatat di sistem.
            </p>

            <!-- DETAIL -->
            <div class="mt-6 bg-gray-50 rounded-xl p-4 text-sm text-gray-600 text-left space-y-3">

                <div class="flex justify-between">
                    <span class="text-gray-400">Status</span>
                    <span class="font-medium text-green-600">Tercatat ✓</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-gray-400">Waktu</span>
                    <span class="font-medium text-gray-800">{{ now()->format('d M Y, H:i') }}</span>
                </div>

            </div>

            <p class="text-xs text-gray-400 mt-6">
                Halaman ini bisa ditutup setelah selesai.
            </p>

        </div>

        <p class="text-xs text-gray-400">Smart School Asset Management System</p>

    </div>

</body>

</html>