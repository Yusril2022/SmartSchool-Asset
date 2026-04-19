@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Data Lemari
            </h1>
            <p class="text-gray-500 text-sm">
                Kelola data lemari penyimpanan barang
            </p>
        </div>

        <div class="flex gap-3">

            <!-- SEARCH -->
            <form method="GET" action="{{ route('lemari.index') }}">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari lemari..."
                    class="px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </form>

            <!-- BUTTON -->
            <a href="{{ route('lemari.create') }}"
               class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm transition">
                + Tambah
            </a>

        </div>

    </div>

    <!-- TABLE CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <table class="w-full text-sm text-gray-700">

            <!-- HEAD -->
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Kode</th>
                    <th class="px-6 py-4 text-left">Lemari</th>
                    <th class="px-6 py-4 text-left">Ruangan</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="divide-y divide-gray-100">

                @forelse($lemaris as $lemari)
                <tr class="hover:bg-gray-50 transition">

                    <!-- KODE -->
                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $lemari->kode_lemari }}
                    </td>

                    <!-- NAMA -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-800">
                                {{ $lemari->nama_lemari }}
                            </span>
                            <span class="text-xs text-gray-400">
                                ID: {{ $lemari->id }}
                            </span>
                        </div>
                    </td>

                    <!-- RUANGAN -->
                    <td class="px-6 py-4 text-gray-600">
                        {{ $lemari->room->nama_ruangan ?? '-' }}
                    </td>

                    <!-- ACTION -->
                    <td class="px-6 py-4 text-center">

                        <div class="flex justify-center gap-2">

                            <!-- EDIT -->
                            <a href="{{ route('lemari.edit',$lemari->id) }}"
                               class="px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition text-xs font-medium">
                                Edit
                            </a>

                            <!-- DELETE -->
                            <form action="{{ route('lemari.destroy',$lemari->id) }}" method="POST">
                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Yakin hapus?')"
                                    class="px-3 py-1.5 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition text-xs font-medium">
                                    Hapus
                                </button>
                            </form>

                        </div>

                    </td>

                </tr>

                @empty
                <tr>
                    <td colspan="4" class="text-center py-10 text-gray-500">
                        Data lemari tidak ditemukan
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection