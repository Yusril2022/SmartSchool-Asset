@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Tambah Ruangan
        </h1>
        <p class="text-gray-500 text-sm">
            Tambahkan data ruangan baru
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <form action="{{ route('lokasi-ruangan.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- KODE -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Kode Ruangan
                </label>

                <input type="text" name="kode_ruangan"
                    value="{{ old('kode_ruangan') }}"
                    placeholder="R001"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400">

                @error('kode_ruangan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- NAMA -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Nama Ruangan
                </label>

                <input type="text" name="nama_ruangan"
                    value="{{ old('nama_ruangan') }}"
                    placeholder="Lab Komputer"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400">

                @error('nama_ruangan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-2">

                <a href="{{ route('lokasi-ruangan.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>

                <button
                    class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm transition">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection