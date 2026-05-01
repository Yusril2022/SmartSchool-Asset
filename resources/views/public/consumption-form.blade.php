<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ambil Barang Konsumsi — Smart School Assets</title>
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
        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-center">
            <p class="text-gray-500 text-sm">Pilih barang yang ingin diambil dari daftar di bawah</p>
        </div>

        <!-- ALERT ERROR -->
        @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ $errors->first() }}
        </div>
        @endif

        <!-- FORM -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <form id="formAmbil" action="{{ route('ambil.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- NAMA PENGAMBIL — ketik manual -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Nama Pengambil <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_pengambil" value="{{ old('nama_pengambil') }}"
                        placeholder="Ketik nama lengkap kamu..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('nama_pengambil')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SEBAGAI -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Sebagai <span class="text-red-500">*</span>
                    </label>
                    <select name="sebagai" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Pilih --</option>
                        <option value="murid" {{ old('sebagai') == 'murid'   ? 'selected' : '' }}>Murid</option>
                        <option value="pegawai" {{ old('sebagai') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                    </select>
                    @error('sebagai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PILIH BARANG -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Barang <span class="text-red-500">*</span>
                    </label>
                    <select name="id_barang" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                        <option value="{{ $item->id }}" {{ old('id_barang') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_barang }} (Stok: {{ $item->stok_total }})
                        </option>
                        @endforeach
                    </select>
                    @error('id_barang')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- JUMLAH -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Jumlah Diambil <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="jumlah_ambil" value="{{ old('jumlah_ambil', 1) }}" min="1"
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

</body>

</html>