@extends('layouts.user')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            {{ $barang->nama_barang }}
        </h1>

        <p class="text-gray-500 text-sm">
            {{ $barang->kategori }}
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <!-- INFO GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- STOK -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-500">Stok</p>

                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium
                    {{ $barang->stok_total <= 5 
                        ? 'bg-red-100 text-red-600' 
                        : 'bg-green-100 text-green-600' }}">

                    {{ $barang->stok_total }}
                </span>
            </div>

            <!-- LOKASI -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-sm text-gray-500">Lokasi</p>

                <p class="text-gray-800 text-sm mt-2">
                    {{ $barang->cabinet->nama_lemari ?? '-' }}
                    <span class="text-gray-400">•</span>
                    {{ $barang->cabinet->room->nama_ruangan ?? '-' }}
                </p>
            </div>

        </div>

        <!-- ACTION -->
        <div class="mt-6">

            @if($barang->stok_total > 0)

            <a href="{{ route('peminjaman.create', ['id' => $barang->id]) }}"
               class="block text-center bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl transition shadow">

                Pinjam Barang

            </a>

            @else

            <button
                class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl cursor-not-allowed">

                Stok Habis

            </button>

            @endif

        </div>

    </div>

</div>

@endsection