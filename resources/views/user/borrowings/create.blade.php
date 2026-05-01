@extends('layouts.user')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <a href="{{ route('items.user') }}"
            class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-orange-500 transition mb-3">
            ← Kembali ke Daftar Barang
        </a>
        <h1 class="text-2xl font-semibold text-gray-800">Form Peminjaman</h1>
        <p class="text-gray-500 text-sm">Isi detail peminjaman barang di bawah ini</p>
    </div>

    {{-- INFO BARANG --}}
    <div class="bg-orange-50 border border-orange-100 rounded-2xl p-5 flex gap-4 items-start">

        @if ($item->foto)
        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_barang }}"
            class="w-20 h-20 rounded-xl object-cover border border-orange-100 shrink-0">
        @else
        <div class="w-20 h-20 rounded-xl bg-orange-100 flex items-center justify-center text-3xl shrink-0">
            📦
        </div>
        @endif

        <div class="flex-1 min-w-0">
            <p class="text-xs text-orange-400 font-mono mb-0.5">{{ $item->kode_barang }}</p>
            <h2 class="text-lg font-semibold text-gray-800 truncate">{{ $item->nama_barang }}</h2>
            <p class="text-sm text-gray-500">{{ $item->kategori }}</p>

            <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                <span>📍 {{ $item->cabinet->nama_lemari ?? '-' }} ·
                    {{ $item->cabinet->room->nama_ruangan ?? '-' }}</span>
                <span class="font-semibold {{ $item->stok_kritis ? 'text-red-500' : 'text-green-600' }}">
                    Stok: {{ $item->stok_total }}
                </span>
            </div>
        </div>

    </div>

    {{-- ERROR --}}
    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    {{-- FORM --}}
    <form action="{{ route('borrowings.store') }}" method="POST" class="space-y-5">
        @csrf

        <input type="hidden" name="id_barang" value="{{ $item->id }}">

        {{-- JUMLAH --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Jumlah Pinjam <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center gap-3">
                <button type="button" onclick="ubahJumlah(-1)"
                    class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-xl text-lg font-bold text-gray-600 transition">
                    −
                </button>
                <input type="number" id="jumlah_pinjam" name="jumlah_pinjam" value="{{ old('jumlah_pinjam', 1) }}"
                    min="1" max="{{ $item->stok_total }}"
                    class="flex-1 text-center border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-400 @error('jumlah_pinjam') border-red-400 @enderror">
                <button type="button" onclick="ubahJumlah(1)"
                    class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-xl text-lg font-bold text-gray-600 transition">
                    +
                </button>
            </div>
            @error('jumlah_pinjam')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-400 mt-1">Stok tersedia: {{ $item->stok_total }}</p>
        </div>

        {{-- TANGGAL KEMBALI --}}
        @if ($item->harga <= 10000000) <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal Kembali <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}"
                min="{{ now()->addDay()->format('Y-m-d') }}"
                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('tanggal_kembali') border-red-400 @enderror">
            @error('tanggal_kembali')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
</div>
@else
<div class="bg-blue-50 border border-blue-100 text-blue-700 px-4 py-3 rounded-xl text-sm">
    ℹ️ Barang bernilai di atas Rp 10 juta. Tanggal kembali akan ditentukan bersama admin.
</div>
@endif

{{-- CATATAN --}}
<div class="bg-yellow-50 border border-yellow-100 rounded-xl px-4 py-3 text-sm text-yellow-700">
    📋 Pengajuan peminjaman akan menunggu persetujuan admin sebelum diproses.
</div>

{{-- SUBMIT --}}
<button type="submit"
    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 rounded-xl transition shadow-sm">
    Ajukan Peminjaman
</button>

</form>

</div>

@endsection

@section('scripts')
<script>
function ubahJumlah(delta) {
    const input = document.getElementById('jumlah_pinjam');
    const max = parseInt(input.max) || 999;
    let val = parseInt(input.value) || 1;
    val = Math.min(max, Math.max(1, val + delta));
    input.value = val;
}
</script>
@endsection