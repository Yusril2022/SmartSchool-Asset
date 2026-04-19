@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Data Peminjaman
        </h1>
        <p class="text-gray-500 text-sm">
            Kelola proses peminjaman dan pengembalian barang
        </p>
    </div>

    <!-- TABLE -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <table class="w-full text-sm text-gray-700">

            <!-- HEAD -->
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">User</th>
                    <th class="px-6 py-4 text-left">Barang</th>
                    <th class="px-6 py-4 text-left">Jumlah</th>
                    <th class="px-6 py-4 text-left">Tgl Pinjam</th>
                    <th class="px-6 py-4 text-left">Tenggat</th>
                    <th class="px-6 py-4 text-left">Tgl Kembali</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y divide-gray-100">

                @foreach($data as $item)
                <tr class="hover:bg-gray-50 transition">

                    <!-- USER -->
                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $item->user->name ?? '-' }}
                    </td>

                    <!-- BARANG -->
                    <td class="px-6 py-4">
                        {{ $item->item->nama_barang ?? '-' }}
                    </td>

                    <!-- JUMLAH -->
                    <td class="px-6 py-4">
                        {{ $item->jumlah_pinjam }}
                    </td>

                    <!-- TGL PINJAM -->
                    <td class="px-6 py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}
                    </td>

                    <!-- TENGGAT -->
                    <td class="px-6 py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($item->tenggat_kembali)->format('d M Y') }}
                    </td>

                    <!-- TGL KEMBALI -->
                    <td class="px-6 py-4">
                        @if($item->tanggal_kembali)
                            {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    <!-- STATUS -->
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            {{ $item->status == 'pending' ? 'bg-yellow-100 text-yellow-600' : '' }}
                            {{ $item->status == 'dipinjam' ? 'bg-orange-100 text-orange-600' : '' }}
                            {{ $item->status == 'dikembalikan' ? 'bg-green-100 text-green-600' : '' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>

                    <!-- AKSI -->
                    <td class="px-6 py-4 text-center">

                        <div class="flex justify-center gap-2">

                            <!-- APPROVE -->
                            @if($item->status == 'pending')
                            <form action="{{ route('admin.peminjaman.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="action" value="approve">

                                <button
                                    class="px-3 py-1.5 rounded-lg bg-green-100 text-green-600 hover:bg-green-200 transition text-xs font-medium">
                                    Approve
                                </button>
                            </form>
                            @endif

                            <!-- KEMBALIKAN -->
                            @if($item->status == 'dipinjam')
                            <form action="{{ route('admin.peminjaman.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="action" value="kembali">

                                <button
                                    class="px-3 py-1.5 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition text-xs font-medium">
                                    Kembalikan
                                </button>
                            </form>
                            @endif

                        </div>

                    </td>

                </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection