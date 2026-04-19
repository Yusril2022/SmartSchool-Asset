@extends('layouts.user')

@section('content')

<div class="space-y-6">

    <!-- GREETING -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <h1 class="text-xl font-semibold text-gray-800">
            Halo, {{ auth()->user()->name }} 👋
        </h1>

        <p class="text-gray-500 text-sm mt-1">
            Selamat datang di sistem peminjaman barang
        </p>

    </div>


    <!-- QUICK ACTION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- PINJAM -->
        <a href="{{ route('barang.user') }}"
           class="bg-orange-500 hover:bg-orange-600 transition rounded-2xl p-6 text-white shadow-sm flex flex-col gap-2">

            <span class="text-lg font-semibold">
                📦 Pinjam Barang
            </span>

            <span class="text-sm text-orange-100">
                Lihat daftar barang yang tersedia
            </span>

        </a>

        <!-- RIWAYAT -->
        <a href="{{ route('peminjaman.index') }}"
           class="bg-white border border-gray-200 hover:bg-gray-50 transition rounded-2xl p-6 text-gray-800 shadow-sm flex flex-col gap-2">

            <span class="text-lg font-semibold">
                📄 Riwayat Peminjaman
            </span>

            <span class="text-sm text-gray-500">
                Lihat status dan histori peminjaman
            </span>

        </a>

    </div>


    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-gray-500 text-sm">Total Barang</p>
            <h2 class="text-2xl font-bold text-gray-800 mt-1">120</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-gray-500 text-sm">Sedang Dipinjam</p>
            <h2 class="text-2xl font-bold text-orange-500 mt-1">5</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-gray-500 text-sm">Dikembalikan</p>
            <h2 class="text-2xl font-bold text-green-500 mt-1">10</h2>
        </div>

    </div>


    <!-- NOTICE -->
    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 text-sm text-orange-700">

        💡 Pastikan mengembalikan barang tepat waktu agar tidak terkena sanksi.

    </div>

</div>

@endsection