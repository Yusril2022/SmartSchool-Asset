@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Tambah Barang Masuk</h1>
        <p class="text-gray-500 text-sm">Catat penambahan stok barang</p>
    </div>

    <!-- ALERT ERROR -->
    @if(session('error'))
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <form action="{{ route('incoming-items.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- PILIH BARANG -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Barang <span class="text-red-500">*</span>
                </label>
                <select name="id_barang" required
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ old('id_barang') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_barang }}
                        ({{ $item->kode_barang }})
                        — Stok: {{ $item->stok_total }}
                    </option>
                    @endforeach
                </select>
                @error('id_barang')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- JUMLAH MASUK -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Jumlah Masuk <span class="text-red-500">*</span>
                </label>
                <input type="number" name="jumlah_masuk" value="{{ old('jumlah_masuk') }}" min="1"
                    placeholder="Contoh: 10"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                @error('jumlah_masuk')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- TANGGAL MASUK -->
            <div>
                <label class="block text-sm text-gray-600 mb-1">
                    Tanggal Masuk <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', now()->format('Y-m-d')) }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                @error('tanggal_masuk')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- INFO -->
            <div class="bg-orange-50 text-orange-700 px-4 py-3 rounded-lg text-sm">
                ℹ️ Stok barang akan otomatis bertambah setelah data disimpan.
            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('incoming-items.index') }}"
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