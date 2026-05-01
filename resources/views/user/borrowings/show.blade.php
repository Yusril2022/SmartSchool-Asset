@extends('layouts.user')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('borrowings.index') }}"
            class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-orange-500 transition mb-3">
            ← Kembali ke Riwayat
        </a>
        <h1 class="text-2xl font-semibold text-gray-800">Detail Peminjaman</h1>
        <p class="text-gray-500 text-sm font-mono">{{ $borrowing->kode_peminjaman }}</p>
    </div>

    {{-- STATUS BANNER --}}
    @php
    $bannerConfig = match($borrowing->status) {
    'pending' => ['bg-yellow-50 border-yellow-200 text-yellow-700', '⏳ Menunggu persetujuan admin'],
    'dipinjam' => ['bg-orange-50 border-orange-200 text-orange-700', '📦 Barang sedang dipinjam'],
    'dikembalikan' => ['bg-green-50 border-green-200 text-green-700', '✅ Barang telah dikembalikan'],
    'ditolak' => ['bg-red-50 border-red-200 text-red-600', '❌ Pengajuan ditolak admin'],
    default => ['bg-gray-50 border-gray-200 text-gray-600', ucfirst($borrowing->status)],
    };
    @endphp
    <div class="border rounded-xl px-4 py-3 text-sm font-medium {{ $bannerConfig[0] }}">
        {{ $bannerConfig[1] }}
    </div>

    {{-- INFO BARANG --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <p class="text-xs uppercase tracking-wider text-gray-400 font-medium">Informasi Barang</p>
        </div>

        <div class="p-5 flex gap-4 items-start">

            @if ($borrowing->item->foto)
            <img src="{{ asset('storage/' . $borrowing->item->foto) }}" alt="{{ $borrowing->item->nama_barang }}"
                class="w-20 h-20 rounded-xl object-cover border border-gray-100 shrink-0">
            @else
            <div class="w-20 h-20 rounded-xl bg-orange-50 flex items-center justify-center text-3xl shrink-0">
                📦
            </div>
            @endif

            <div class="flex-1 space-y-1">
                <p class="text-xs text-gray-400 font-mono">{{ $borrowing->item->kode_barang }}</p>
                <p class="font-semibold text-gray-800">{{ $borrowing->item->nama_barang }}</p>
                <p class="text-sm text-gray-500">{{ $borrowing->item->kategori }}</p>
                <p class="text-xs text-gray-400">
                    📍 {{ $borrowing->item->cabinet->nama_lemari ?? '-' }}
                    · {{ $borrowing->item->cabinet->room->nama_ruangan ?? '-' }}
                </p>
            </div>

        </div>

    </div>

    {{-- DETAIL PEMINJAMAN --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <p class="text-xs uppercase tracking-wider text-gray-400 font-medium">Detail Peminjaman</p>
        </div>

        <div class="divide-y divide-gray-100 text-sm">

            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Jumlah Pinjam</span>
                <span class="font-semibold text-gray-800">{{ $borrowing->jumlah_pinjam }}</span>
            </div>

            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Tanggal Pinjam</span>
                <span class="text-gray-700">
                    {{ \Carbon\Carbon::parse($borrowing->tanggal_peminjaman)->format('d M Y, H:i') }}
                </span>
            </div>

            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Tanggal Kembali</span>
                <span class="text-gray-700">
                    @if ($borrowing->tanggal_kembali)
                    {{ \Carbon\Carbon::parse($borrowing->tanggal_kembali)->format('d M Y') }}
                    @else
                    <span class="text-gray-400 italic">Ditentukan admin</span>
                    @endif
                </span>
            </div>

            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Diproses oleh</span>
                <span class="text-gray-700">
                    {{ $borrowing->admin->name ?? '-' }}
                </span>
            </div>

            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Status</span>
                @php
                $badge = match($borrowing->status) {
                'pending' => 'bg-yellow-100 text-yellow-600',
                'dipinjam' => 'bg-orange-100 text-orange-600',
                'dikembalikan' => 'bg-green-100 text-green-600',
                'ditolak' => 'bg-red-100 text-red-500',
                default => 'bg-gray-100 text-gray-500',
                };
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">
                    {{ ucfirst($borrowing->status) }}
                </span>
            </div>

        </div>

    </div>

    {{-- DOKUMEN (jika ada) --}}
    @if ($borrowing->documents && $borrowing->documents->count())
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <p class="text-xs uppercase tracking-wider text-gray-400 font-medium">Dokumen Berita Acara</p>
        </div>

        <ul class="divide-y divide-gray-100">
            @foreach ($borrowing->documents as $doc)
            <li class="flex items-center justify-between px-5 py-3 text-sm">
                <span class="text-gray-700">📄 {{ $doc->nama_dokumen ?? 'Dokumen' }}</span>
                <a href="{{ route('documents.download', $doc->id) }}"
                    class="text-orange-500 hover:underline text-xs font-medium">
                    Download
                </a>
            </li>
            @endforeach
        </ul>

    </div>
    @endif

</div>

@endsection