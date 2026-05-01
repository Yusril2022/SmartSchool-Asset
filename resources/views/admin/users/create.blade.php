@extends('layouts.admin')

@section('content')

<div class="max-w-2xl mx-auto space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">Tambah User</h1>
        <p class="text-gray-500 text-sm">Buat akun baru untuk siswa atau guru</p>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ $errors->first() }}
    </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- NAMA -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NOMOR INDUK -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Nomor Induk <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nomor_induk" value="{{ old('nomor_induk') }}" required
                        placeholder="NIS / NIP"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('nomor_induk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NO HP -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">No HP <span class="text-red-500">*</span></label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('no_hp') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- ROLE -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Role <span class="text-red-500">*</span></label>
                    <select name="role" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-orange-400">
                        <option value="">-- Pilih Role --</option>
                        <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="guru" {{ old('role') == 'guru'  ? 'selected' : '' }}>Guru</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-2 rounded-lg border border-gray-200 text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('users.index') }}"
                    class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </a>
                <button class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg shadow-sm transition">
                    Simpan
                </button>
            </div>

        </form>
    </div>

</div>

@endsection