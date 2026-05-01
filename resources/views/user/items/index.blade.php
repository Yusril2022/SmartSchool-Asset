@extends('layouts.user')

@section('content')

<div class="space-y-6">

    <!-- HEADER + SEARCH -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Daftar Barang
            </h1>
            <p class="text-gray-500 text-sm">
                Pilih barang yang ingin dipinjam
            </p>
        </div>

        <form method="GET" class="w-full md:w-80">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..."
                class="w-full px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400">
        </form>

    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse($barangs as $barang)

        <!-- CARD -->
        <div
            class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">

            <!-- TOP -->
            <div class="space-y-2">

                <!-- NAMA -->
                <h2 class="font-semibold text-lg text-gray-800 hover:text-orange-500 transition">
                    {{ $barang->nama_barang }}
                </h2>

                <!-- KATEGORI -->
                <p class="text-sm text-gray-500">
                    {{ $barang->kategori }}
                </p>

                <!-- LOKASI -->
                <p class="text-xs text-gray-400">
                    {{ $barang->cabinet->nama_lemari ?? '-' }} •
                    {{ $barang->cabinet->room->nama_ruangan ?? '-' }}
                </p>

                <!-- STOK -->
                <div class="flex justify-between items-center mt-3">

                    <span class="text-sm text-gray-500">
                        Stok
                    </span>

                    <span class="px-2 py-1 text-xs rounded-full font-medium
                    {{ $barang->stok_total <= 5 
                        ? 'bg-red-100 text-red-600' 
                        : 'bg-green-100 text-green-600' }}">

                        {{ $barang->stok_total }}
                    </span>

                </div>

                <!-- <div class="flex justify-center mt-4 bg-white p-3 rounded-lg border">
                    {!! QrCode::size(110)
                    ->margin(1)
                    ->generate(url('/scan/' . $barang->kode_barang)) !!}
                </div> -->

            </div>

            <!-- BUTTON -->
            <div class="mt-5">
                <a href="{{ route('items.user.show', $barang->id) }}"
                    class="block text-center bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition shadow-sm">

                    Lihat Detail

                </a>
            </div>

        </div>

        @empty
        <div class="col-span-3 text-center text-gray-500 py-10">
            Tidak ada barang tersedia
        </div>
        @endforelse

    </div>

</div>

@endsection