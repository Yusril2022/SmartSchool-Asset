@extends('layouts.user')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Riwayat Peminjaman</h1>
            <p class="text-gray-500 text-sm">Daftar pengajuan dan status peminjaman barang kamu</p>
        </div>
        <a href="{{ route('items.user') }}"
            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm">
            + Pinjam Barang
        </a>
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

    {{-- TABLE --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-gray-700">

            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Kode</th>
                    <th class="px-6 py-4 text-left">Barang</th>
                    <th class="px-6 py-4 text-left">Jumlah</th>
                    <th class="px-6 py-4 text-left">Tgl Pinjam</th>
                    <th class="px-6 py-4 text-left">Tgl Kembali</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-center">Detail</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($data as $p)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 font-mono text-xs text-gray-500">
                        {{ $p->kode_peminjaman }}
                    </td>

                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $p->item->nama_barang ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $p->jumlah_pinjam }}
                    </td>

                    <td class="px-6 py-4 text-gray-500 text-xs">
                        {{ \Carbon\Carbon::parse($p->tanggal_peminjaman)->format('d M Y') }}
                    </td>

                    <td class="px-6 py-4 text-gray-500 text-xs">
                        @if ($p->tanggal_kembali)
                        {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d M Y') }}
                        @else
                        <span class="text-gray-400 italic">Ditentukan admin</span>
                        @endif
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
                        $label = match($p->status) {
                        'pending' => '⏳ Pending',
                        'dipinjam' => '📦 Dipinjam',
                        'dikembalikan' => '✅ Dikembalikan',
                        'ditolak' => '❌ Ditolak',
                        default => ucfirst($p->status),
                        };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('borrowings.show', $p->id) }}"
                            class="text-xs text-orange-500 hover:underline font-medium">
                            Lihat →
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-14 text-gray-400">
                        <div class="text-4xl mb-2">📄</div>
                        <p>Belum ada riwayat peminjaman</p>
                        <a href="{{ route('items.user') }}"
                            class="inline-block mt-3 text-orange-500 hover:underline text-sm">
                            Pinjam barang sekarang →
                        </a>
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