@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">{{ $document->judul_dokumen }}</h1>
            <p class="text-gray-500 text-sm">{{ $document->jenis_dokumen }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('documents.download', $document->id) }}"
                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition">
                Download
            </a>
            <a href="{{ route('documents.index') }}"
                class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 text-sm transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- INFO DOKUMEN -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">

            <div>
                <p class="text-gray-400">No. Dokumen</p>
                <p class="text-gray-800 font-medium mt-1">{{ $document->no_dokumen ?? '-' }}</p>
            </div>

            <div>
                <p class="text-gray-400">Tanggal</p>
                <p class="text-gray-800 font-medium mt-1">
                    {{ $document->tanggal_dokumen->format('d M Y') }}
                </p>
            </div>

            <div>
                <p class="text-gray-400">Pihak Terkait</p>
                <p class="text-gray-800 font-medium mt-1">{{ $document->pihak_terkait ?? '-' }}</p>
            </div>

            <div>
                <p class="text-gray-400">Barang Terkait</p>
                <p class="text-gray-800 font-medium mt-1">{{ $document->item->nama_barang ?? '-' }}</p>
            </div>

            <div>
                <p class="text-gray-400">Diupload Oleh</p>
                <p class="text-gray-800 font-medium mt-1">{{ $document->uploadedBy->name ?? '-' }}</p>
            </div>

            @if($document->keterangan)
            <div class="col-span-2 md:col-span-3">
                <p class="text-gray-400">Keterangan</p>
                <p class="text-gray-800 mt-1">{{ $document->keterangan }}</p>
            </div>
            @endif

        </div>
    </div>

    <!-- PREVIEW FILE -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-medium text-gray-700 mb-4">Preview Dokumen</h3>

        @if(in_array($extension, ['jpg', 'jpeg', 'png']))
        {{-- Gambar langsung tampil --}}
        <img src="{{ $url }}" class="max-w-full rounded-lg border border-gray-200 shadow-sm"
            alt="{{ $document->judul_dokumen }}">

        @elseif($extension === 'pdf')
        {{-- PDF embed di browser --}}
        <iframe src="{{ $url }}" class="w-full rounded-lg border border-gray-200" style="height: 700px;"
            frameborder="0">
            Browser kamu tidak support preview PDF.
            <a href="{{ $url }}" class="text-orange-500 underline">Klik di sini untuk membuka.</a>
        </iframe>

        @else
        <div class="text-center py-10 text-gray-400">
            <p class="text-4xl mb-2">📄</p>
            <p>Format file tidak mendukung preview.</p>
            <a href="{{ route('documents.download', $document->id) }}"
                class="text-orange-500 underline text-sm mt-2 inline-block">
                Download untuk melihat file
            </a>
        </div>
        @endif

    </div>

</div>

@endsection