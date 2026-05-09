<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Barang Konsumsi — Smart School Assets</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-lg space-y-5">

        {{-- HEADER --}}
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">Smart School Assets</h1>
            <p class="text-gray-500 text-sm mt-1">Form Pengambilan Barang Konsumsi</p>
        </div>

        {{-- FLASH ERROR --}}
        @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-xl text-sm">
            ⚠️ {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-xl text-sm">
            ⚠️ {{ $errors->first() }}
        </div>
        @endif

        {{-- FORM --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-5">
            <form id="formAmbil" action="{{ route('ambil.store') }}" method="POST">
                @csrf

                {{-- NAMA PENGAMBIL --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Pengambil <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pengambil" value="{{ old('nama_pengambil') }}"
                        placeholder="Ketik nama lengkap kamu..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('nama_pengambil')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SEBAGAI --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sebagai <span class="text-red-500">*</span>
                    </label>
                    <select name="sebagai" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Pilih --</option>
                        <option value="murid" {{ old('sebagai') == 'murid'   ? 'selected' : '' }}>Murid</option>
                        <option value="pegawai" {{ old('sebagai') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    </select>
                    @error('sebagai')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DAFTAR BARANG --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Barang yang Diambil <span class="text-red-500">*</span>
                    </label>

                    {{-- Data barang untuk JS — taruh di data attribute supaya Prettier tidak merusak --}}
                    <div id="barang-list" class="space-y-3"
                        data-items="{{ htmlspecialchars(json_encode($items->keyBy('id')), ENT_QUOTES, 'UTF-8') }}">

                        {{-- Baris pertama — tidak bisa dihapus --}}
                        <div class="barang-row flex gap-2 items-start">
                            <div class="flex-1">
                                <select name="items[0][id_barang]" required
                                    class="barang-select w-full px-3 py-2.5 rounded-xl border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($items as $item)
                                    <option value="{{ $item->id }}" data-stok="{{ $item->stok_total }}"
                                        {{ old('items.0.id_barang') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_barang }} (Stok: {{ $item->stok_total }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-24">
                                <input type="number" name="items[0][jumlah_ambil]"
                                    value="{{ old('items.0.jumlah_ambil', 1) }}" min="1" placeholder="Jml"
                                    class="jumlah-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm text-center">
                            </div>
                            <div class="w-9 h-10 flex items-center justify-center text-transparent select-none">✕</div>
                        </div>

                    </div>

                    {{-- TOMBOL TAMBAH BARANG --}}
                    <button type="button" id="btnTambah"
                        class="mt-3 w-full border border-dashed border-orange-400 text-orange-500 hover:bg-orange-50 py-2 rounded-xl text-sm font-medium transition flex items-center justify-center gap-2">
                        <span class="text-lg leading-none">+</span> Tambah Barang
                    </button>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl transition shadow font-semibold">
                    Ambil Barang
                </button>

            </form>
        </div>

        <p class="text-center text-xs text-gray-400 pb-4">
            Smart School Asset Management System
        </p>

    </div>

    <script>
    // Ambil data dari data attribute — decode HTML entity dulu sebelum parse
    const barangList = document.getElementById('barang-list');
    const tmp = document.createElement('textarea');
    tmp.innerHTML = barangList.dataset.items;
    const itemsData = JSON.parse(tmp.value);
    const btnTambah = document.getElementById('btnTambah');
    let rowIndex = 1;

    function buildOptions(selectedId) {
        let opts = '<option value="">-- Pilih Barang --</option>';
        Object.values(itemsData).forEach(function(item) {
            var sel = item.id == selectedId ? 'selected' : '';
            opts += '<option value="' + item.id + '" data-stok="' + item.stok_total + '" ' + sel + '>' +
                item.nama_barang + ' (Stok: ' + item.stok_total + ')' +
                '</option>';
        });
        return opts;
    }

    btnTambah.addEventListener('click', function() {
        var row = document.createElement('div');
        row.className = 'barang-row flex gap-2 items-start';
        row.innerHTML =
            '<div class="flex-1">' +
            '<select name="items[' + rowIndex + '][id_barang]" required' +
            ' class="barang-select w-full px-3 py-2.5 rounded-xl border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">' +
            buildOptions('') +
            '</select>' +
            '</div>' +
            '<div class="w-24">' +
            '<input type="number" name="items[' + rowIndex + '][jumlah_ambil]"' +
            ' value="1" min="1" placeholder="Jml"' +
            ' class="jumlah-input w-full px-3 py-2.5 rounded-xl border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm text-center">' +
            '</div>' +
            '<div class="w-9">' +
            '<button type="button" onclick="hapusBaris(this)"' +
            ' class="w-9 h-10 flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-400 rounded-xl transition text-sm">✕</button>' +
            '</div>';
        barangList.appendChild(row);
        rowIndex++;
    });

    function hapusBaris(btn) {
        btn.closest('.barang-row').remove();
    }

    document.getElementById('formAmbil').addEventListener('submit', function(e) {
        var rows = document.querySelectorAll('.barang-row');
        var selected = [];
        var valid = true;

        rows.forEach(function(row) {
            var sel = row.querySelector('.barang-select');
            var jml = row.querySelector('.jumlah-input');
            var itemId = sel.value;
            var jumlah = parseInt(jml.value) || 0;

            if (!itemId) {
                alert('Pilih barang di semua baris atau hapus baris yang kosong.');
                valid = false;
                return;
            }

            var stok = parseInt(itemsData[itemId] ? itemsData[itemId].stok_total : 0) || 0;
            var existing = null;
            for (var i = 0; i < selected.length; i++) {
                if (selected[i].id === itemId) {
                    existing = selected[i];
                    break;
                }
            }
            var totalAmbil = existing ? existing.jumlah + jumlah : jumlah;

            if (totalAmbil > stok) {
                alert('Stok ' + (itemsData[itemId] ? itemsData[itemId].nama_barang : '') +
                    ' tidak cukup. Stok: ' + stok + ', diminta: ' + totalAmbil);
                valid = false;
                return;
            }

            if (existing) {
                existing.jumlah += jumlah;
            } else {
                selected.push({
                    id: itemId,
                    jumlah: jumlah
                });
            }
        });

        if (!valid) e.preventDefault();
    });
    </script>

</body>

</html>