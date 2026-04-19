@extends('layouts.user')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Riwayat Peminjaman
        </h1>
        <p class="text-gray-500 text-sm">
            Daftar aktivitas peminjaman barang Anda
        </p>
    </div>

    <!-- TABLE CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <table class="w-full text-sm text-gray-700">

            <!-- HEAD -->
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Kode</th>
                    <th class="px-6 py-4 text-left">Barang</th>
                    <th class="px-6 py-4 text-left">Jumlah</th>
                    <th class="px-6 py-4 text-left">Status</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y divide-gray-100">

                @forelse($data as $p)
                <tr class="hover:bg-gray-50 transition">

                    <!-- KODE -->
                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $p->kode_peminjaman }}
                    </td>

                    <!-- BARANG -->
                    <td class="px-6 py-4">
                        {{ $p->item->nama_barang ?? '-' }}
                    </td>

                    <!-- JUMLAH -->
                    <td class="px-6 py-4">
                        {{ $p->jumlah_pinjam }}
                    </td>

                    <!-- STATUS -->
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                        {{ $p->status == 'pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                        {{ $p->status == 'dipinjam' ? 'bg-orange-100 text-orange-600' : '' }}
                        {{ $p->status == 'dikembalikan' ? 'bg-green-100 text-green-600' : '' }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-10 text-gray-500">
                        Belum ada data peminjaman
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection