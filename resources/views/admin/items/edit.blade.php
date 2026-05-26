@extends('layouts.admin')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Edit Barang
        </h1>
        <p class="text-gray-500 text-sm">
            Perbarui data barang
        </p>
    </div>

    <!-- CARD -->
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

        <!-- ERROR -->
        @if ($errors->any())
        <div class="bg-red-100 text-red-600 p-3 mb-4 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('items.update', $barang->id) }}" method="POST" enctype="multipart/form-data" class="
            space-y-6">
            @csrf
            @method('PUT')

            <!-- GRID -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- NAMA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Nama Barang
                    </label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- KATEGORI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Kategori
                    </label>
                    <input type="text" name="kategori" value="{{ old('kategori', $barang->kategori) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- LEMARI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Lemari</label>
                    <select name="id_lemari" id="selectLemari"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Tidak ada lemari --</option>
                        @foreach($lemaris as $lemari)
                        <option value="{{ $lemari->id }}" data-ruangan="{{ $lemari->id_ruangan }}"
                            {{ old('id_lemari', $barang->id_lemari) == $lemari->id ? 'selected' : '' }}
                            {{ $lemari->nama_lemari }} - {{ $lemari->room->nama_ruangan }} </option>
                            @endforeach
                    </select>
                </div>

                <!-- RUANGAN — muncul jika tidak pilih lemari -->
                <div id="divRuangan" class="{{ old('id_lemari', $barang->id_lemari) ? 'hidden' : '' }}">
                    <label class="block text-sm text-gray-600 mb-1">
                        Ruangan <span class="text-red-500">*</span>
                    </label>
                    <select name="id_ruangan" id="selectRuangan"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach($rooms as $room)
                        <option value="{{ $room->id }}"
                            {{ old('id_ruangan', $barang->id_ruangan) == $room->id ? 'selected' : '' }}>
                            {{ $room->nama_ruangan }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- JENIS BARANG -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Jenis Barang</label>
                    <select name="jenis_barang"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="aset"
                            {{ old('jenis_barang', $barang->jenis_barang) == 'aset' ? 'selected' : '' }}>Aset</option>
                        <option value="konsumsi"
                            {{ old('jenis_barang', $barang->jenis_barang) == 'konsumsi' ? 'selected' : '' }}>Konsumsi
                        </option>
                    </select>
                    @error('jenis_barang')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- BATAS MINIMUM -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Batas Minimum</label>
                    <input type="number" name="batas_minimum" value="{{ old('batas_minimum', $barang->batas_minimum) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('batas_minimum')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- HARGA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Harga</label>
                    <input type="number" name="harga" value="{{ old('harga', $barang->harga) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('harga')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- KONDISI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Kondisi Barang</label>
                    <select name="kondisi"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="Baik" {{ old('kondisi', $barang->kondisi) == 'Baik'         ? 'selected' : '' }}>
                            Baik</option>
                        <option value="Rusak Ringan"
                            {{ old('kondisi', $barang->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan
                        </option>
                        <option value="Rusak Sedang"
                            {{ old('kondisi', $barang->kondisi) == 'Rusak Sedang' ? 'selected' : '' }}>Rusak Sedang
                        </option>
                        <option value="Rusak Berat"
                            {{ old('kondisi', $barang->kondisi) == 'Rusak Berat'  ? 'selected' : '' }}>Rusak Berat
                        </option>
                        <option value="Mati Total"
                            {{ old('kondisi', $barang->kondisi) == 'Mati Total'   ? 'selected' : '' }}>Mati Total
                        </option>
                    </select>
                    @error('kondisi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CATATAN KONDISI -->
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">
                        Catatan Perubahan Kondisi
                        <span class="text-gray-400 text-xs font-normal">(opsional)</span>
                    </label>
                    <input type="text" name="catatan_kondisi" value="{{ old('catatan_kondisi') }}"
                        placeholder="Contoh: Layar retak akibat terjatuh"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- FOTO -->
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Foto Barang</label>

                    {{-- Tampilkan foto lama jika ada --}}
                    @if($barang->foto)
                    <img src="{{ Storage::url($barang->foto) }}"
                        class="w-24 h-24 object-cover rounded-lg mb-2 border border-gray-200">
                    @endif

                    <input type="file" name="foto" accept="image/jpg,image/jpeg,image/png"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700">
                    <p class="text-xs text-gray-400 mt-1">
                        Kosongkan jika tidak ingin mengubah foto. Maks 2MB.
                    </p>
                    @error('foto')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-4">

                <a href="{{ route('items.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>

                <button class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm transition">
                    Update
                </button>

            </div>

        </form>
        @if($barang->conditionLogs->count() > 0)
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">

            <h3 class="text-sm font-semibold text-gray-700 mb-4">
                📋 Riwayat Perubahan Kondisi
            </h3>

            <div class="space-y-3">
                @foreach($barang->conditionLogs->sortByDesc('created_at') as $log)
                <div class="flex items-start gap-3 text-sm">

                    <!-- ICON -->
                    <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center shrink-0 mt-0.5">
                        <span class="text-orange-500 text-xs">🔄</span>
                    </div>

                    <!-- DETAIL -->
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- KONDISI LAMA -->
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                {{ $log->kondisi_lama ?? 'Baru' }}
                            </span>

                            <span class="text-gray-400">→</span>

                            <!-- KONDISI BARU -->
                            @php
                            $badge = match($log->kondisi_baru) {
                            'Baik' => 'bg-green-100 text-green-700',
                            'Rusak Ringan' => 'bg-yellow-100 text-yellow-700',
                            'Rusak Sedang' => 'bg-orange-100 text-orange-700',
                            'Rusak Berat' => 'bg-red-100 text-red-700',
                            'Mati Total' => 'bg-gray-200 text-gray-600',
                            default => 'bg-gray-100 text-gray-500',
                            };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ $log->kondisi_baru }}
                            </span>
                        </div>

                        <div class="text-xs text-gray-400 mt-1">
                            {{ $log->admin->name ?? '-' }} •
                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}
                        </div>

                        @if($log->catatan)
                        <p class="text-xs text-gray-500 mt-1 italic">
                            "{{ $log->catatan }}"
                        </p>
                        @endif
                    </div>

                </div>
                @endforeach
            </div>

        </div>
        @endif

        <script>
        const selectLemari = document.getElementById('selectLemari');
        const divRuangan = document.getElementById('divRuangan');
        const selectRuangan = document.getElementById('selectRuangan');

        selectLemari.addEventListener('change', function() {
            if (this.value === '') {
                divRuangan.classList.remove('hidden');
                selectRuangan.required = true;
            } else {
                divRuangan.classList.add('hidden');
                selectRuangan.required = false;
                selectRuangan.value = '';
            }
        });
        </script>

    </div>

</div>

@endsection