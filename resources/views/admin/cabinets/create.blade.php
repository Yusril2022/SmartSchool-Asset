@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Tambah Lemari
        </h1>
        <p class="text-gray-500 text-sm">
            Tambahkan lemari baru untuk penyimpanan barang
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <form method="POST" action="{{ route('cabinets.store') }}" class="space-y-6">
            @csrf

            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- KODE -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Kode Lemari
                    </label>

                    <input name="kode_lemari" value="{{ old('kode_lemari') }}" placeholder="Contoh: L001"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400">

                    @error('kode_lemari')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NAMA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Nama Lemari
                    </label>

                    <input name="nama_lemari" value="{{ old('nama_lemari') }}" placeholder="Contoh: Lemari A"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400">

                    @error('nama_lemari')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- RUANGAN -->
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">
                        Lokasi Ruangan
                    </label>

                    <select name="id_ruangan"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">

                        <option value="">Pilih Ruangan</option>

                        @foreach($ruangans as $r)
                        <option value="{{ $r->id }}" {{ old('id_ruangan') == $r->id ? 'selected' : '' }}>
                            {{ $r->nama_ruangan }}
                        </option>
                        @endforeach

                    </select>

                    @error('id_ruangan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-2">

                <a href="{{ route('cabinets.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>

                <button class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm transition">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection