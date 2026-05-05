@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Upload Dokumen</h1>
        <p class="text-gray-500 text-sm">Tambahkan dokumen baru ke arsip</p>
    </div>

    @if($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- JUDUL -->
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">
                        Judul Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul_dokumen" value="{{ old('judul_dokumen') }}"
                        placeholder="Contoh: Berita Acara Peminjaman Laptop Asus"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('judul_dokumen')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- JENIS -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Jenis Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="jenis_dokumen" value="{{ old('jenis_dokumen') }}"
                        placeholder="Contoh: MOU, Surat Hibah, Berita Acara"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('jenis_dokumen')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NO DOKUMEN -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">No. Dokumen</label>
                    <input type="text" name="no_dokumen" value="{{ old('no_dokumen') }}"
                        placeholder="Contoh: MOU/2024/001"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- TANGGAL -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">
                        Tanggal Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_dokumen"
                        value="{{ old('tanggal_dokumen', now()->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('tanggal_dokumen')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIHAK TERKAIT -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Pihak Terkait</label>
                    <input type="text" name="pihak_terkait" value="{{ old('pihak_terkait') }}"
                        placeholder="Contoh: Kemendikbud, CV Maju Jaya"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>

                <!-- FILE UPLOAD -->
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">
                        Upload File <span class="text-gray-400 text-xs">(PDF/JPG/PNG, maks 5MB)</span>
                    </label>
                    <input type="file" name="file_path" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700">
                    @error('file_path')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- KETERANGAN -->
                <div class="md:col-span-2">
                    <label class="block text-sm text-gray-600 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="3" placeholder="Catatan tambahan..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('keterangan') }}</textarea>
                </div>

            </div>

            <!-- ACTION -->
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('documents.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>
                <button class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm transition">
                    Simpan Dokumen
                </button>
            </div>

        </form>
    </div>

</div>

@endsection