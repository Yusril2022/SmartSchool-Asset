@extends('layouts.user')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Pinjam Barang
        </h1>
        <p class="text-gray-500 text-sm">
            Isi data peminjaman barang
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <form action="{{ route('peminjaman.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- BARANG -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Barang
                </label>

                <input type="text"
                    value="{{ $barang->nama_barang }}"
                    readonly
                    class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-700">
            </div>

            <!-- JUMLAH -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Jumlah Pinjam
                </label>

                <input type="number" name="jumlah_pinjam"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-orange-400 focus:outline-none"
                    required>
            </div>

            <!-- BUTTON -->
            <div class="pt-2">
                <button
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl transition shadow">

                    Pinjam

                </button>
            </div>

        </form>

    </div>

</div>

@endsection