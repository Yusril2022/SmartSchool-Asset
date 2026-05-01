@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Tambah Barang
        </h1>
        <p class="text-gray-500 text-sm">
            Tambahkan data barang baru ke sistem inventory
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

        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- KODE -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Kode Barang
                    </label>
                    <input type="text" name="kode_barang" value="{{ old('kode_barang') }}" placeholder="BRG001"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- NAMA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Nama Barang
                    </label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required
                        placeholder="Laptop Asus"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- LEMARI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Lemari
                    </label>
                    <select name="id_lemari" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">

                        <option value="">-- Pilih Lemari --</option>

                        @foreach($lemaris as $lemari)
                        <option value="{{ $lemari->id }}" {{ old('id_lemari') == $lemari->id ? 'selected' : '' }}>
                            {{ $lemari->nama_lemari }} - {{ $lemari->room->nama_ruangan }}
                        </option>
                        @endforeach

                    </select>
                </div>

                <!-- KATEGORI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Kategori
                    </label>
                    <input type="text" name="kategori" value="{{ old('kategori') }}" required placeholder="Elektronik"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- JENIS -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Jenis Barang
                    </label>
                    <select name="jenis_barang" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">

                        <option value="aset">Aset</option>
                        <option value="konsumsi">Habis Pakai</option>

                    </select>
                </div>

                <!-- MERK -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Merk / Brand</label>
                    <input type="text" name="merk" value="{{ old('merk') }}" placeholder="Contoh: Asus, Canon, Pilot"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- HASIL PEROLEHAN -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Hasil Perolehan</label>
                    <select name="hasil_perolehan"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Pilih --</option>
                        <option value="pembelian" {{ old('hasil_perolehan') == 'pembelian'  ? 'selected' : '' }}>
                            Pembelian</option>
                        <option value="hibah" {{ old('hasil_perolehan') == 'hibah'      ? 'selected' : '' }}>Hibah
                        </option>
                        <option value="sumbangan" {{ old('hasil_perolehan') == 'sumbangan'  ? 'selected' : '' }}>
                            Sumbangan</option>
                        <option value="dana_bos" {{ old('hasil_perolehan') == 'dana_bos'   ? 'selected' : '' }}>Dana BOS
                        </option>
                    </select>
                </div>

                <!-- STOK -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Stok Awal
                    </label>
                    <input type="number" name="stok_awal" value="{{ old('stok_awal') }}" required placeholder="10"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- BATAS MINIMUM -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Batas Minimum
                    </label>
                    <input type="number" name="batas_minimum" value="{{ old('batas_minimum') }}" placeholder="5"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- HARGA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Harga
                    </label>
                    <input type="number" name="harga" value="{{ old('harga') }}" placeholder="1000000"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Foto Barang</label>
                    <input type="file" name="foto" accept="image/*"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700">
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
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

@endsection