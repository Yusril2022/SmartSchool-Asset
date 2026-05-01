@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Barang Masuk</h1>
            <p class="text-gray-500 text-sm">Riwayat penambahan stok barang</p>
        </div>
        <a href="{{ route('incoming-items.create') }}"
            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm transition">
            + Tambah Barang Masuk
        </a>
    </div>

    <!-- ALERT -->
    @if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- TABLE -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-gray-700">

            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Tanggal</th>
                    <th class="px-6 py-4 text-left">Barang</th>
                    <th class="px-6 py-4 text-left">Kategori</th>
                    <th class="px-6 py-4 text-left">Jumlah Masuk</th>
                    <th class="px-6 py-4 text-left">Stok Sekarang</th>
                    <th class="px-6 py-4 text-left">Dicatat Oleh</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($data as $row)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d M Y') }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-800">
                                {{ $row->item->nama_barang ?? '-' }}
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ $row->item->kode_barang ?? '' }}
                            </span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $row->item->kategori ?? '-' }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                            +{{ $row->jumlah_masuk }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium
                            {{ ($row->item->stok_kritis ?? false) ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $row->item->stok_total ?? '-' }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $row->admin->name ?? '-' }}
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">
                        Belum ada data barang masuk
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