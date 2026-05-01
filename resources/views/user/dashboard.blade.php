@extends('layouts.user')

@section('content')

<div class="space-y-6">

    <!-- GREETING -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <h1 class="text-xl font-semibold text-gray-800">
            Halo, {{ auth()->user()->name }} 👋
        </h1>

        <p class="text-gray-500 text-sm mt-1">
            Selamat datang di sistem peminjaman barang
        </p>

    </div>


    <!-- QUICK ACTION -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- PINJAM -->
        <a href="{{ route('items.user') }}"
            class="bg-orange-500 hover:bg-orange-600 transition rounded-2xl p-6 text-white shadow-sm flex flex-col gap-2">

            <span class="text-lg font-semibold">
                📦 Pinjam Barang
            </span>

            <span class="text-sm text-orange-100">
                Lihat daftar barang yang tersedia
            </span>

        </a>

        <!-- RIWAYAT -->
        <a href="{{ route('borrowings.index') }}"
            class="bg-white border border-gray-200 hover:bg-gray-50 transition rounded-2xl p-6 text-gray-800 shadow-sm flex flex-col gap-2">

            <span class="text-lg font-semibold">
                📄 Riwayat Peminjaman
            </span>

            <span class="text-sm text-gray-500">
                Lihat status dan histori peminjaman
            </span>

        </a>

    </div>


    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-gray-500 text-sm">Sedang Dipinjam</p>
            <h2 class="text-2xl font-bold text-orange-500 mt-1">{{ $totalPinjamSaya }}</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-gray-500 text-sm">Menunggu Approve</p>
            <h2 class="text-2xl font-bold text-yellow-500 mt-1">{{ $totalPendingSaya }}</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <p class="text-gray-500 text-sm">Sudah Dikembalikan</p>
            <h2 class="text-2xl font-bold text-green-500 mt-1">{{ $totalKembaliSaya }}</h2>
        </div>

    </div>

    @if($peminjamanSaya->count() > 0)
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-medium text-gray-800">Peminjaman Terbaru</h3>
        </div>
        <table class="w-full text-sm text-gray-700">
            <tbody class="divide-y divide-gray-100">
                @foreach($peminjamanSaya as $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium">{{ $p->item->nama_barang ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $p->jumlah_pinjam }} unit</td>
                    <td class="px-6 py-3">
                        @php
                        $badge = match($p->status) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'dipinjam' => 'bg-blue-100 text-blue-700',
                        'dikembalikan' => 'bg-green-100 text-green-700',
                        'ditolak' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600',
                        };
                        @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- NOTICE -->
    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 text-sm text-orange-700">

        💡 Pastikan mengembalikan barang tepat waktu agar tidak terkena sanksi.

    </div>

</div>

@endsection