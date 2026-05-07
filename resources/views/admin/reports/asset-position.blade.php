@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Print Posisi Aset</h1>
        <p class="text-gray-500 text-sm">Cetak laporan posisi aset per ruangan</p>
    </div>

    <!-- FORM -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('asset-position.print') }}" method="GET" target="_blank" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Pilih Ruangan
                    <span class="text-gray-400 font-normal text-xs ml-1">(kosongkan untuk cetak semua ruangan)</span>
                </label>

                <div class="space-y-2 max-h-72 overflow-y-auto border border-gray-200 rounded-xl p-4">
                    @foreach($rooms as $room)
                    @php
                    $totalAset = $room->cabinets->sum(fn($c) => $c->items->count());
                    @endphp
                    <label class="flex items-center gap-3 cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition">
                        <input type="checkbox" name="room_ids[]" value="{{ $room->id }}"
                            class="w-4 h-4 text-orange-500 rounded border-gray-300 focus:ring-orange-400">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-800">{{ $room->nama_ruangan }}</span>
                            <span class="text-xs text-gray-400 ml-2">({{ $totalAset }} aset)</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- INFO -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-sm text-orange-700">
                💡 Laporan hanya menampilkan barang <strong>Aset</strong>. Barang habis pakai tidak termasuk.
            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm transition">
                    🖨️ Cetak Laporan
                </button>
            </div>

        </form>
    </div>

</div>

@endsection