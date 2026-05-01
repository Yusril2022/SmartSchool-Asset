<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Barang — {{ $item->nama_barang }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">

    <div class="w-full max-w-md space-y-6">

        <!-- HEADER -->
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">Smart School Assets</h1>
            <p class="text-gray-500 text-sm mt-1">Form Pengambilan Barang Konsumsi</p>
        </div>

        <!-- INFO BARANG -->
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-4">
                @if($item->foto)
                <img src="{{ Storage::url($item->foto) }}"
                    class="w-16 h-16 object-cover rounded-xl border border-gray-200">
                @else
                <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center text-2xl">
                    📦
                </div>
                @endif
                <div>
                    <h2 class="font-semibold text-gray-800">{{ $item->nama_barang }}</h2>
                    <p class="text-sm text-gray-500">{{ $item->kategori }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        Stok tersedia:
                        <span class="font-semibold text-green-600">{{ $item->stok_total }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- ALERT ERROR -->
        @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
        @endif

        <!-- FORM -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <form action="{{ route('public.item-usages.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="id_barang" value="{{ $item->id }}">

                <!-- NAMA PENGAMBIL -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Nama Pengambil <span class="text-red-500">*</span>
                    </label>

                    <!-- DROPDOWN SEARCH -->
                    <select name="nama_pengambil" id="nama_pengambil"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Pilih Nama --</option>
                        @foreach($pegawais as $pegawai)
                        <option value="{{ $pegawai->nama }}"
                            {{ old('nama_pengambil') == $pegawai->nama ? 'selected' : '' }}>
                            {{ $pegawai->nama }}
                        </option>
                        @endforeach
                        <option value="__manual__">Lainnya (isi manual)</option>
                    </select>

                    <!-- INPUT MANUAL — muncul jika pilih "Lainnya" -->
                    <input type="text" id="nama_manual" placeholder="Ketik nama lengkap..."
                        class="w-full mt-2 px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400 hidden">

                    @error('nama_pengambil')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- JUMLAH -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Jumlah Diambil <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_ambil" value="{{ old('jumlah_ambil', 1) }}" min="1"
                        max="{{ $item->stok_total }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('jumlah_ambil')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl transition shadow font-medium">
                    Ambil Barang
                </button>

            </form>
        </div>

        <p class="text-center text-xs text-gray-400">
            Smart School Asset Management System
        </p>

    </div>

    <!-- SCRIPT: handle input manual -->
    <script>
    const select = document.getElementById('nama_pengambil');
    const manualInput = document.getElementById('nama_manual');

    select.addEventListener('change', function() {
        if (this.value === '__manual__') {
            manualInput.classList.remove('hidden');
            manualInput.required = true;
            // kosongkan value select supaya validasi tidak lewat __manual__
            manualInput.addEventListener('input', function() {
                select.value = this.value;
            });
        } else {
            manualInput.classList.add('hidden');
            manualInput.required = false;
            manualInput.value = '';
        }
    });
    </script>

</body>

</html>