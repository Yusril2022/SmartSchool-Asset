@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('admin.borrowings.index') }}"
            class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-orange-500 transition mb-3">
            ← Kembali ke Daftar Peminjaman
        </a>
        <h1 class="text-2xl font-semibold text-gray-800">Detail Peminjaman</h1>
        <p class="text-gray-500 text-sm font-mono">{{ $borrowing->kode_peminjaman }}</p>
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

    {{-- STATUS BANNER --}}
    @php
    $bannerConfig = match($borrowing->status) {
    'pending' => ['bg-yellow-50 border-yellow-200 text-yellow-700', '⏳ Menunggu persetujuan admin'],
    'dipinjam' => ['bg-orange-50 border-orange-200 text-orange-700', '📦 Barang sedang dipinjam'],
    'dikembalikan' => ['bg-green-50 border-green-200 text-green-700', '✅ Barang telah dikembalikan'],
    'ditolak' => ['bg-red-50 border-red-200 text-red-600', '❌ Pengajuan ditolak'],
    default => ['bg-gray-50 border-gray-200 text-gray-600', ucfirst($borrowing->status)],
    };
    @endphp
    <div class="border rounded-xl px-4 py-3 text-sm font-medium {{ $bannerConfig[0] }}">
        {{ $bannerConfig[1] }}
    </div>

    {{-- INFO PEMINJAM --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <p class="text-xs uppercase tracking-wider text-gray-400 font-medium">Informasi Peminjam</p>
        </div>

        <div class="divide-y divide-gray-100 text-sm">
            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Nama</span>
                <span class="font-medium text-gray-800">{{ $borrowing->user->name ?? '-' }}</span>
            </div>
            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Email</span>
                <span class="text-gray-700">{{ $borrowing->user->email ?? '-' }}</span>
            </div>
            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Role</span>
                <span class="capitalize text-gray-700">{{ $borrowing->user->role ?? '-' }}</span>
            </div>
        </div>

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

            <div class="flex-1 space-y-1 text-sm">
                <p class="text-xs text-gray-400 font-mono">{{ $borrowing->item->kode_barang }}</p>
                <p class="font-semibold text-gray-800">{{ $borrowing->item->nama_barang }}</p>
                <p class="text-gray-500">{{ $borrowing->item->kategori }}</p>
                <p class="text-xs text-gray-400">
                    📍 {{ $borrowing->item->cabinet->nama_lemari ?? '-' }}
                    · {{ $borrowing->item->cabinet->room->nama_ruangan ?? '-' }}
                </p>
                <p class="text-xs text-gray-500">
                    Harga: <span class="font-medium">Rp {{ number_format($borrowing->item->harga, 0, ',', '.') }}</span>
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
                    <span class="text-gray-400 italic">Belum ditentukan</span>
                    @endif
                </span>
            </div>

            <div class="flex justify-between px-5 py-3">
                <span class="text-gray-400">Diproses oleh</span>
                <span class="text-gray-700">{{ $borrowing->admin->name ?? '-' }}</span>
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

    {{-- BERITA ACARA --}}
    @php
    $beritaAcara = $borrowing->documents
    ->where('jenis_dokumen', 'Berita Acara Peminjaman')
    ->first();
    @endphp

    @if ($beritaAcara)
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-xs uppercase tracking-wider text-gray-400 font-medium">Berita Acara Peminjaman</p>
            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">
                Auto-generated
            </span>
        </div>

        <div class="px-5 py-4 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-800">{{ $beritaAcara->judul_dokumen }}</p>
                <p class="text-xs text-gray-400 mt-0.5">
                    No. {{ $beritaAcara->no_dokumen ?? '-' }} ·
                    {{ $beritaAcara->tanggal_dokumen->format('d M Y') }}
                </p>
            </div>
            <a href="{{ route('documents.download', $beritaAcara->id) }}"
                class="inline-flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition">
                ⬇ Download PDF
            </a>
        </div>

        <div class="px-5 pb-4">
            <p class="text-xs text-gray-400">
                💡 Cetak dokumen ini, tanda tangani oleh peminjam dan
                {{ $beritaAcara->uploadedBy->jabatan ?? 'Kepala Sekolah' }},
                lalu scan dan upload ke menu <a href="{{ route('documents.index') }}"
                    class="text-orange-500 hover:underline">Arsip Dokumen</a>.
            </p>
        </div>

    </div>
    @elseif ($borrowing->status === 'dipinjam' && $borrowing->item->harga > 10_000_000)
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl px-5 py-4 text-sm text-yellow-700">
        ⚠️ Berita acara belum ter-generate. Coba approve ulang atau hubungi developer.
    </div>
    @endif

    {{-- TOMBOL AKSI ADMIN --}}
    @if ($borrowing->status === 'pending')

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 space-y-3">

        <p class="text-sm font-medium text-gray-700 mb-1">Tindakan Admin</p>

        {{-- APPROVE --}}
        <form action="{{ route('admin.borrowings.update', $borrowing->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="action" value="approve">

            {{-- Tanggal kembali hanya muncul jika harga > 10 juta (belum ditentukan user) --}}
            @if ($borrowing->item->harga > 10_000_000 && !$borrowing->tanggal_kembali)
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tentukan Tanggal Kembali <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_kembali" min="{{ now()->addDay()->format('Y-m-d') }}" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                <p class="text-xs text-gray-400 mt-1">
                    Barang bernilai di atas Rp 10 juta — tanggal kembali ditentukan admin
                </p>
            </div>
            @endif

            <button type="submit" onclick="return confirm('Setujui peminjaman ini?')"
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 rounded-xl transition">
                ✅ Setujui Peminjaman
            </button>
        </form>

        {{-- TOLAK --}}
        <form action="{{ route('admin.borrowings.update', $borrowing->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="action" value="tolak">
            <button type="submit" onclick="return confirm('Tolak peminjaman ini?')"
                class="w-full bg-red-50 hover:bg-red-100 text-red-500 font-semibold py-3 rounded-xl transition border border-red-200">
                ❌ Tolak Peminjaman
            </button>
        </form>

    </div>

    @elseif ($borrowing->status === 'dipinjam')

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">

        <p class="text-sm font-medium text-gray-700 mb-3">Tindakan Admin</p>

        {{-- KEMBALIKAN --}}
        <form action="{{ route('admin.borrowings.update', $borrowing->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="action" value="kembali">
            <button type="submit" onclick="return confirm('Tandai barang ini sudah dikembalikan?')"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition">
                🔄 Tandai Sudah Dikembalikan
            </button>
        </form>

    </div>

    @endif

</div>

@endsection