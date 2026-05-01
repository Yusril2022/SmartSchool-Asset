@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Riwayat Pengambilan Barang</h1>
            <p class="text-gray-500 text-sm">Track record pengambilan barang konsumsi</p>
        </div>
    </div>

    <!-- ALERT -->
    @if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- FILTER -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.item-usages.index') }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <!-- FILTER TANGGAL DARI -->
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tanggal Dari</label>
                <input type="date" name="dari" value="{{ request('dari') }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <!-- FILTER TANGGAL SAMPAI -->
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tanggal Sampai</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <!-- FILTER NAMA PENGAMBIL -->
            <div>
                <label class="block text-xs text-gray-500 mb-1">Nama Pengambil</label>
                <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <!-- FILTER BARANG -->
            <div>
                <label class="block text-xs text-gray-500 mb-1">Barang</label>
                <select name="barang"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">-- Semua Barang --</option>
                    @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ request('barang') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_barang }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- TOMBOL -->
            <div class="md:col-span-4 flex gap-3 justify-end">
                <a href="{{ route('admin.item-usages.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                    Reset
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition text-sm">
                    Filter
                </button>
            </div>

        </form>
    </div>

    <!-- SUMMARY -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $data->total() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500">Total Diambil</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">{{ $totalDiambil }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500">Oleh Murid</p>
            <p class="text-2xl font-bold text-blue-500 mt-1">{{ $totalMurid }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-gray-500">Oleh Pegawai</p>
            <p class="text-2xl font-bold text-green-500 mt-1">{{ $totalPegawai }}</p>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-gray-700">

            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Tanggal</th>
                    <th class="px-6 py-4 text-left">Nama Pengambil</th>
                    <th class="px-6 py-4 text-left">Sebagai</th>
                    <th class="px-6 py-4 text-left">Barang</th>
                    <th class="px-6 py-4 text-left">Jumlah</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($data as $row)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($row->tanggal_ambil)->format('d M Y, H:i') }}
                    </td>

                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $row->nama_pengambil ?? $row->user->name ?? '-' }}
                    </td>

                    <td class="px-6 py-4">
                        @if($row->sebagai)
                        <span
                            class="px-2 py-1 rounded-full text-xs font-medium
                                {{ $row->sebagai === 'murid' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ ucfirst($row->sebagai) }}
                        </span>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $row->item->nama_barang ?? '-' }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            {{ $row->jumlah_ambil }}
                        </span>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">
                        Belum ada data pengambilan
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>

        @if($data->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $data->links() }}
        </div>
        @endif

    </div>

</div>

@endsection