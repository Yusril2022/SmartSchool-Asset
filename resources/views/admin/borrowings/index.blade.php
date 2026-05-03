@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Manajemen Peminjaman</h1>
        <p class="text-gray-500 text-sm">Kelola seluruh pengajuan peminjaman barang</p>
    </div>

    {{-- FLASH --}}
    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
        ✅ {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    {{-- FILTER STATUS --}}
    <div class="flex flex-wrap gap-2">
        @php
        $statuses = ['semua' => 'Semua', 'pending' => '⏳ Pending', 'dipinjam' => '📦 Dipinjam', 'dikembalikan' => '✅
        Dikembalikan', 'ditolak' => '❌ Ditolak'];
        $aktif = request('status', 'semua');
        @endphp
        @foreach ($statuses as $val => $label)
        <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}" class="px-4 py-1.5 rounded-full text-xs font-medium border transition
               {{ $aktif === $val
                   ? 'bg-orange-500 text-white border-orange-500'
                   : 'bg-white text-gray-600 border-gray-200 hover:border-orange-300' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
        <form method="GET" action="{{ route('admin.borrowings.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">

            <!-- PERTAHANKAN filter status yang sudah ada -->
            <input type="hidden" name="status" value="{{ request('status', 'semua') }}">

            <!-- SEARCH NAMA PEMINJAM -->
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-500 mb-1">Cari Nama Peminjam</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama peminjam..."
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <!-- TANGGAL DARI -->
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <!-- TANGGAL SAMPAI -->
            <div>
                <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}"
                    class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <!-- TOMBOL -->
            <div class="md:col-span-4 flex justify-end gap-3">
                @if(request('search') || request('dari') || request('sampai'))
                <a href="{{ route('admin.borrowings.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm">
                    Reset
                </a>
                @endif
                <button type="submit"
                    class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition text-sm">
                    Filter
                </button>
            </div>

        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-gray-700">

            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Kode</th>
                    <th class="px-6 py-4 text-left">Peminjam</th>
                    <th class="px-6 py-4 text-left">Barang</th>
                    <th class="px-6 py-4 text-left">Jumlah</th>
                    <th class="px-6 py-4 text-left">Tgl Pinjam</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($data as $p)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 font-mono text-xs text-gray-500">
                        {{ $p->kode_peminjaman }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $p->user->name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $p->user->email ?? '' }}</div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $p->item->nama_barang ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $p->item->kode_barang ?? '' }}</div>
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $p->jumlah_pinjam }}
                    </td>

                    <td class="px-6 py-4 text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($p->tanggal_peminjaman)->format('d M Y') }}
                    </td>

                    <td class="px-6 py-4">
                        @php
                        $badge = match($p->status) {
                        'pending' => 'bg-yellow-100 text-yellow-600',
                        'dipinjam' => 'bg-orange-100 text-orange-600',
                        'dikembalikan' => 'bg-green-100 text-green-600',
                        'ditolak' => 'bg-red-100 text-red-500',
                        default => 'bg-gray-100 text-gray-500',
                        };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.borrowings.show', $p->id) }}"
                            class="px-3 py-1.5 rounded-lg bg-orange-50 text-orange-500 hover:bg-orange-100 transition text-xs font-medium">
                            Detail →
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-14 text-gray-400">
                        <div class="text-4xl mb-2">📄</div>
                        <p>Tidak ada data peminjaman</p>
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($data->hasPages())
    <div class="flex justify-center">
        {{ $data->links() }}
    </div>
    @endif

</div>

@endsection