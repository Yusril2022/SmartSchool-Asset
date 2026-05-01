@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Edit Barang
        </h1>
        <p class="text-gray-500 text-sm">
            Perbarui data barang
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <!-- ERROR -->
        @if ($errors->any())
        <div class="bg-red-100 text-red-600 p-3 mb-4 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('items.update', $barang->id) }}" method="POST" enctype="multipart/form-data" class="
            space-y-6">
            @csrf
            @method('PUT')

            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- NAMA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Nama Barang
                    </label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- KATEGORI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Kategori
                    </label>
                    <input type="text" name="kategori" value="{{ old('kategori', $barang->kategori) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- LEMARI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Lemari
                    </label>
                    <select name="id_lemari"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">

                        @foreach($lemaris as $l)
                        <option value="{{ $l->id }}"
                            {{ old('id_lemari', $barang->id_lemari) == $l->id ? 'selected' : '' }}>
                            {{ $l->nama_lemari }} - {{ $l->room->nama_ruangan }}
                        </option>
                        @endforeach

                    </select>
                </div>

                <!-- JENIS BARANG -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Jenis Barang</label>
                    <select name="jenis_barang"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="aset"
                            {{ old('jenis_barang', $barang->jenis_barang) == 'aset' ? 'selected' : '' }}>Aset</option>
                        <option value="konsumsi"
                            {{ old('jenis_barang', $barang->jenis_barang) == 'konsumsi' ? 'selected' : '' }}>Konsumsi
                        </option>
                    </select>
                    @error('jenis_barang')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- BATAS MINIMUM -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Batas Minimum</label>
                    <input type="number" name="batas_minimum" value="{{ old('batas_minimum', $barang->batas_minimum) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('batas_minimum')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- HARGA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Harga</label>
                    <input type="number" name="harga" value="{{ old('harga', $barang->harga) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('harga')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- FOTO -->
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Foto Barang</label>

                    {{-- Tampilkan foto lama jika ada --}}
                    @if($barang->foto)
                    <img src="{{ Storage::url($barang->foto) }}"
                        class="w-24 h-24 object-cover rounded-lg mb-2 border border-gray-200">
                    @endif

                    <input type="file" name="foto" accept="image/jpg,image/jpeg,image/png"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700">
                    <p class="text-xs text-gray-400 mt-1">
                        Kosongkan jika tidak ingin mengubah foto. Maks 2MB.
                    </p>
                    @error('foto')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-4">

                <a href="{{ route('items.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>

                <button class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm transition">
                    Update
                </button>

            </div>

        </form>

    </div>

</div>

@endsection