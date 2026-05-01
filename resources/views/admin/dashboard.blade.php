@extends('layouts.admin')

@section('content')

<div class="p-6 space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Dashboard Admin
        </h1>
        <p class="text-gray-500 text-sm">
            Overview of your inventory system
        </p>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Total Barang</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalBarang }}</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Total Stok</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalAset }} Aset / {{ $totalKonsumsi }} Konsumsi</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Barang Dipinjam</p>
            <h2 class="text-3xl font-bold text-orange-500 mt-2">{{ $totalPinjam }}</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Barang Dikembalikan</p>
            <h2 class="text-3xl font-bold text-green-500 mt-2">{{ $totalKembali }}</h2>
        </div>

        <!-- STOK KRITIS -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Stok Kritis</p>
            <h2 class="text-3xl font-bold mt-2 {{ $stokKritis > 0 ? 'text-red-500' : 'text-gray-800' }}">
                {{ $stokKritis }}
            </h2>
            <p class="text-xs text-gray-400 mt-1">Barang di bawah batas minimum</p>
        </div>

        <!-- PENDING -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Menunggu Approve</p>
            <h2 class="text-3xl font-bold mt-2 {{ $totalPending > 0 ? 'text-yellow-500' : 'text-gray-800' }}">
                {{ $totalPending }}
            </h2>
            <p class="text-xs text-gray-400 mt-1">Pengajuan peminjaman pending</p>
        </div>

    </div>

    <!-- CHART + ACTIVITY -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- CHART -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-gray-800 font-semibold mb-4">
                Statistik Peminjaman
            </h3>

            <canvas id="chartPeminjaman" data-labels="{{ json_encode($chartLabels) }}"
                data-values="{{ json_encode($chartValues) }}">
            </canvas>
        </div>

        <!-- ACTIVITY -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-gray-800 font-semibold mb-4">
                Aktivitas Terbaru
            </h3>

            <ul class="space-y-3 text-sm text-gray-600">
                @forelse($peminjamanPending as $p)
                <li class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
                    <span>
                        <span class="font-medium">{{ $p->user->name ?? '-' }}</span>
                        mengajukan pinjam
                        <span class="font-medium">{{ $p->item->nama_barang ?? '-' }}</span>
                    </span>
                </li>
                @empty
                <li class="text-gray-400">Tidak ada pengajuan pending</li>
                @endforelse
            </ul>
        </div>

    </div>

</div>

<!-- CHART SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/chart-init.js') }}"></script>

@endsection