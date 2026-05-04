@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Data Barang
            </h1>
            <p class="text-gray-500 text-sm">
                Kelola seluruh data barang inventory
            </p>
        </div>

        <div class="flex gap-3">

            <!-- FILTER -->
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                <form method="GET" action="{{ route('items.index') }}" class="flex flex-col md:flex-row gap-3">

                    <!-- SEARCH -->
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang..."
                        class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">

                    <!-- FILTER JENIS -->
                    <select name="jenis"
                        class="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="semua" {{ request('jenis', 'semua') == 'semua' ? 'selected' : '' }}>Semua Jenis
                        </option>
                        <option value="aset" {{ request('jenis') == 'aset'     ? 'selected' : '' }}>Aset</option>
                        <option value="konsumsi" {{ request('jenis') == 'konsumsi' ? 'selected' : '' }}>Habis Pakai
                        </option>
                    </select>

                    <!-- TOMBOL -->
                    <button type="submit"
                        class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition text-sm">
                        Cari
                    </button>

                    @if(request('search') || request('jenis'))
                    <a href="{{ route('items.index') }}"
                        class="px-5 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                        Reset
                    </a>
                    @endif

                </form>
            </div>

            <!-- BUTTON -->
            <a href="{{ route('items.create') }}"
                class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm transition">
                Tambah
            </a>

        </div>

    </div>

    <!-- TABLE -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <table class="w-full text-sm text-gray-700">

            <!-- HEAD -->
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Kode</th>
                    <th class="px-6 py-4 text-left">Nama</th>
                    <th class="px-6 py-4 text-left">Kategori</th>
                    <th class="px-6 py-4 text-left">Lokasi</th>
                    <th class="px-6 py-4 text-left">Stok</th>
                    <th class="px-6 py-4 text-center">QR</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y divide-gray-100">

                @forelse($barangs as $barang)
                <tr class="hover:bg-gray-50 transition">

                    <!-- KODE -->
                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $barang->kode_barang }}
                    </td>

                    <!-- NAMA -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-800">
                                {{ $barang->nama_barang }}
                            </span>
                        </div>
                    </td>

                    <!-- KATEGORI -->
                    <td class="px-6 py-4 text-gray-600">
                        {{ $barang->kategori }}
                    </td>

                    <!-- LOKASI -->
                    <td class="px-6 py-4 text-sm">
                        <div class="flex flex-col">
                            <span class="text-gray-700">
                                {{ $barang->cabinet->nama_lemari ?? '-' }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ $barang->cabinet->room->nama_ruangan ?? '-' }}
                            </span>
                        </div>
                    </td>

                    <!-- STOK -->
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $barang->stok_total <= 5 
                                ? 'bg-red-100 text-red-600' 
                                : 'bg-green-100 text-green-600' }}">
                            {{ $barang->stok_total }}
                        </span>
                    </td>

                    <!-- QR -->
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center">
                            <div class="p-2 bg-white border border-gray-200 rounded-lg">
                                {!! QrCode::size(70)->generate(url('/scan/' . $barang->kode_barang)) !!}
                            </div>
                        </div>
                    </td>

                    <!-- AKSI -->
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">

                            <!-- EDIT -->
                            <a href="{{ route('items.edit', $barang->id) }}"
                                class="px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition text-xs font-medium">
                                Edit
                            </a>

                            <!-- QR DOWNLOAD -->
                            <a href="{{ route('items.qr', $barang->id) }}" target="_blank"
                                class="px-3 py-1.5 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition text-xs font-medium">
                                QR
                            </a>

                            <!-- DELETE -->
                            <form action="{{ route('items.destroy', $barang->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Yakin hapus?')"
                                    class="px-3 py-1.5 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition text-xs font-medium">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="7" class="text-center py-10 text-gray-500">
                        Tidak ada data barang
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

        @if($barangs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $barangs->links() }}
        </div>
        @endif

    </div>

</div>

@endsection