@extends('layouts.user')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Form Peminjaman
        </h1>
        <p class="text-gray-500 text-sm">
            Isi jumlah barang yang ingin dipinjam
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <form action="{{ route('borrowings.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- BARANG -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Barang
                </label>

                <input type="text" value="{{ $item->nama_barang }}" readonly
                    class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-200 text-gray-700">
            </div>

            <!-- HIDDEN -->
            <input type="hidden" name="id_barang" value="{{ $item->id }}">

            <!-- JUMLAH -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Jumlah Pinjam
                </label>

                <input type="number" name="jumlah_pinjam" min="1" placeholder="Masukkan jumlah" required
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            {{-- Field tanggal kembali --}}
            @if($item->harga <= 10000000) <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Tanggal Kembali <span class="text-red-500">*</span>
                </label>

                <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}"
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">

                @error('tanggal_kembali')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
    </div>
    @else
    <div class="bg-blue-50 text-blue-700 px-4 py-3 rounded-lg text-sm">
        ℹ️ Barang ini bernilai di atas 10 juta. Tanggal kembali ditentukan bersama admin.
    </div>
    @endif

    <!-- BUTTON -->
    <div class="pt-2">
        <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl transition shadow">

            Pinjam Sekarang

        </button>
    </div>

    </form>

</div>

</div>

@endsection