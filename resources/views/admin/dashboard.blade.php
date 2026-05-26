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

    {{-- TOP BARANG DIPINJAM + DIAMBIL --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- TOP DIPINJAM --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-gray-800 font-semibold mb-4">🏆 Top Barang Paling Sering Dipinjam</h3>
            <ul class="space-y-3">
                @forelse($topDipinjam as $i => $row)
                <li class="flex items-center gap-3">
                    <span
                        class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 text-xs font-bold flex items-center justify-center shrink-0">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('items.edit', $row->item->id ?? 0) }}"
                            class="text-sm font-medium text-gray-800 hover:text-orange-500 truncate block">
                            {{ $row->item->nama_barang ?? '-' }}
                        </a>
                        <div class="flex items-center gap-2 mt-0.5">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div class="bg-orange-400 h-1.5 rounded-full"
                                    style="width: {{ $topDipinjam->max('total_pinjam') > 0 ? ($row->total_pinjam / $topDipinjam->max('total_pinjam') * 100) : 0 }}%">
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">{{ $row->total_pinjam }}x</span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="text-gray-400 text-sm">Belum ada data peminjaman</li>
                @endforelse
            </ul>
        </div>

        {{-- TOP DIAMBIL --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-gray-800 font-semibold mb-4">📦 Top Barang Paling Sering Diambil</h3>
            <ul class="space-y-3">
                @forelse($topDiambil as $i => $row)
                <li class="flex items-center gap-3">
                    <span
                        class="w-6 h-6 rounded-full bg-green-100 text-green-600 text-xs font-bold flex items-center justify-center shrink-0">
                        {{ $i + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('items.edit', $row->item->id ?? 0) }}"
                            class="text-sm font-medium text-gray-800 hover:text-green-500 truncate block">
                            {{ $row->item->nama_barang ?? '-' }}
                        </a>
                        <div class="flex items-center gap-2 mt-0.5">
                            <div class="flex-1 bg-gray-100 rounded-full h-1.5">
                                <div class="bg-green-400 h-1.5 rounded-full"
                                    style="width: {{ $topDiambil->max('total_ambil') > 0 ? ($row->total_ambil / $topDiambil->max('total_ambil') * 100) : 0 }} %">
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 shrink-0">{{ $row->total_jumlah }} unit</span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="text-gray-400 text-sm">Belum ada data pengambilan</li>
                @endforelse
            </ul>
        </div>

    </div>

    {{-- PREDIKSI KEBUTUHAN --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-gray-800 font-semibold">🔮 Prediksi Kebutuhan Bulan Depan</h3>
                <p class="text-xs text-gray-400 mt-0.5">Berdasarkan rata-rata pengambilan 3 bulan terakhir</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-400 uppercase border-b border-gray-100">
                        <th class="text-left pb-3">Barang</th>
                        <th class="text-center pb-3">Total 3 Bulan</th>
                        <th class="text-center pb-3">Rata-rata/Bulan</th>
                        <th class="text-center pb-3">Prediksi Bulan Depan</th>
                        <th class="text-center pb-3">Stok Sekarang</th>
                        <th class="text-center pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($prediksi as $row)
                    @php
                    $stok = $row->item->stok_total ?? 0;
                    $pred = $row->prediksi_bulan_depan;
                    $cukup = $stok >= $pred;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="py-3">
                            <a href="{{ route('items.edit', $row->item->id ?? 0) }}"
                                class="font-medium text-gray-800 hover:text-orange-500">
                                {{ $row->item->nama_barang ?? '-' }}
                            </a>
                        </td>
                        <td class="py-3 text-center text-gray-600">{{ $row->total_3bulan }} unit</td>
                        <td class="py-3 text-center text-gray-600">{{ round($row->total_3bulan / 3) }} unit</td>
                        <td class="py-3 text-center font-semibold text-gray-800">{{ $pred }} unit</td>
                        <td
                            class="py-3 text-center {{ $stok <= ($row->item->batas_minimum ?? 0) ? 'text-red-500 font-semibold' : 'text-gray-600' }}">
                            {{ $stok }}
                        </td>
                        <td class="py-3 text-center">
                            @if ($cukup)
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700 font-medium">✅
                                Cukup</span>
                            @else
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-600 font-medium">⚠️ Perlu
                                Tambah</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-400">Belum ada data pengambilan 3 bulan
                            terakhir</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- CHART SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/chart-init.js') }}"></script>

@endsection