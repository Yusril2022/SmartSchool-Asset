@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Arsip Dokumen</h1>
            <p class="text-gray-500 text-sm">Kelola semua dokumen dan berita acara</p>
        </div>
        <a href="{{ route('documents.create') }}"
            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm transition">
            + Upload Dokumen
        </a>
    </div>

    <!-- ALERT -->
    @if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    <!-- TABLE -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-gray-700">

            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Judul Dokumen</th>
                    <th class="px-6 py-4 text-left">Jenis</th>
                    <th class="px-6 py-4 text-left">No. Dokumen</th>
                    <th class="px-6 py-4 text-left">Tanggal</th>
                    <th class="px-6 py-4 text-left">Pihak Terkait</th>
                    <th class="px-6 py-4 text-left">Barang</th>
                    <th class="px-6 py-4 text-left">Diupload Oleh</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-800">{{ $doc->judul_dokumen }}</span>
                            @if($doc->keterangan)
                            <span class="text-xs text-gray-400 mt-0.5">{{ Str::limit($doc->keterangan, 40) }}</span>
                            @endif
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            {{ $doc->jenis_dokumen }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $doc->no_dokumen ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $doc->tanggal_dokumen->format('d M Y') }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $doc->pihak_terkait ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $doc->item->nama_barang ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $doc->uploadedBy->name ?? '-' }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">

                            <a href="{{ route('documents.show', $doc->id) }}"
                                class="px-3 py-1.5 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition text-xs font-medium">
                                Lihat
                            </a>

                            @if($doc->file_path)
                            <a href="{{ route('documents.download', $doc->id) }}"
                                class="px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition text-xs font-medium">
                                Download
                            </a>
                            @else
                            <span class="text-xs text-gray-400 px-3 py-1.5">No file</span>
                            @endif

                            <form action="{{ route('documents.destroy', $doc->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Yakin hapus dokumen ini?')"
                                    class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition text-xs font-medium">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-gray-500">
                        Belum ada dokumen tersimpan
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>

        @if($documents->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $documents->links() }}
        </div>
        @endif

    </div>

</div>

@endsection