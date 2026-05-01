<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Barang — {{ $barang->nama_barang }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md overflow-hidden">

        {{-- HEADER --}}
        <div class="px-6 py-5 text-white {{ $barang->jenis_barang === 'aset' ? 'bg-indigo-600' : 'bg-green-600' }}">
            <p class="text-xs uppercase tracking-widest opacity-75 mb-1">Hasil Scan QR</p>
            <h1 class="text-xl font-bold leading-tight">{{ $barang->nama_barang }}</h1>
            <span class="inline-block mt-2 text-xs font-mono bg-white/20 rounded px-2 py-0.5">
                {{ $barang->kode_barang }}
            </span>
        </div>

        {{-- FOTO --}}
        @if ($barang->foto)
        <div class="w-full h-48 bg-gray-100 overflow-hidden">
            <img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}"
                class="w-full h-full object-cover">
        </div>
        @endif

        {{-- DETAIL --}}
        <div class="px-6 py-5 space-y-3 text-sm text-gray-700">

            <div class="flex justify-between">
                <span class="text-gray-400 font-medium">Kategori</span>
                <span>{{ $barang->kategori }}</span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-400 font-medium">Jenis</span>
                @if ($barang->jenis_barang === 'aset')
                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs font-semibold">Aset</span>
                @else
                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-semibold">Konsumsi</span>
                @endif
            </div>

            @if ($barang->merk)
            <div class="flex justify-between">
                <span class="text-gray-400 font-medium">Merk</span>
                <span>{{ $barang->merk }}</span>
            </div>
            @endif

            <div class="flex justify-between">
                <span class="text-gray-400 font-medium">Lokasi</span>
                <span class="text-right">
                    {{ $barang->cabinet->nama_lemari ?? '-' }}
                    @if ($barang->cabinet?->room)
                    <br><span class="text-xs text-gray-400">{{ $barang->cabinet->room->nama_ruangan }}</span>
                    @endif
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-400 font-medium">Stok Tersedia</span>
                <span class="{{ $barang->stok_kritis ? 'text-red-600 font-semibold' : 'text-gray-800 font-semibold' }}">
                    {{ $barang->stok_total }}
                    @if ($barang->stok_kritis)
                    <span class="text-xs text-red-400 font-normal">(stok kritis)</span>
                    @endif
                </span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-400 font-medium">Harga Satuan</span>
                <span>Rp {{ number_format($barang->harga, 0, ',', '.') }}</span>
            </div>

        </div>

        {{-- TOMBOL AKSI --}}
        <div class="px-6 pb-6 space-y-3">

            @if ($barang->jenis_barang === 'konsumsi')

            {{-- KONSUMSI: langsung ke form publik, tidak perlu login --}}
            @if ($barang->stok_total > 0)
            <a href="{{ route('ambil.form', $barang->kode_barang) }}"
                class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
                📦 Ambil Barang Sekarang
            </a>
            @else
            <div class="w-full text-center bg-gray-100 text-gray-400 font-semibold py-3 rounded-xl">
                ❌ Stok Habis
            </div>
            @endif

            @else

            {{-- ASET: harus login dulu --}}
            @auth
            <a href="{{ route('borrowings.create', $barang->id) }}"
                class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
                📋 Ajukan Peminjaman
            </a>
            @else
            <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-xl transition">
                🔐 Login untuk Meminjam
            </a>
            <p class="text-center text-xs text-gray-400">
                Barang aset memerlukan login untuk mengajukan peminjaman
            </p>
            @endauth

            @endif

        </div>

    </div>

</body>

</html>