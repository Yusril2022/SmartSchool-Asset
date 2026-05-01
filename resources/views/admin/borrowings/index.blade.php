@extends('layouts.admin')

@section('content')
<div class="p-6 min-h-screen bg-gray-50/50">

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Data Peminjaman</h1>
                <p class="text-gray-500 text-sm">Kelola proses peminjaman dan pengembalian barang</p>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700 whitespace-nowrap">
                    <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">User</th>
                            <th class="px-6 py-4 text-left font-semibold">Barang</th>
                            <th class="px-6 py-4 text-left font-semibold">Jumlah</th>
                            <th class="px-6 py-4 text-left font-semibold">Tanggl Pinjam</th>
                            <th class="px-6 py-4 text-left font-semibold">Tanggl Kembali</th>
                            <th class="px-6 py-4 text-left font-semibold">Status</th>
                            <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($data as $item)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $item->user->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $item->item->nama_barang ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-semibold">
                                    {{ $item->jumlah_pinjam }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($item->tanggal_peminjaman)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($item->tanggal_kembali)
                                <span
                                    class="text-gray-600">{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}</span>
                                @else
                                <span class="text-gray-400 italic text-xs">Belum kembali</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'dipinjam' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'dikembalikan' => 'bg-green-100 text-green-700 border-green-200',
                                'ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                ];
                                $class = $statusClasses[$item->status] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold border {{ $class }}">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center items-center gap-2">
                                    @if($item->status == 'pending')
                                    <form action="{{ route('admin.borrowings.update', $item->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="approve">
                                        <button
                                            class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.borrowings.update', $item->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="tolak">
                                        <button onclick="return confirm('Tolak peminjaman ini?')"
                                            class="bg-white border border-red-200 text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                            Tolak
                                        </button>
                                    </form>
                                    @elseif($item->status == 'dipinjam')
                                    <form action="{{ route('admin.borrowings.update', $item->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="action" value="kembali">
                                        <button
                                            class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition shadow-sm">
                                            Kembalikan
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection