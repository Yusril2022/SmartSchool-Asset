@extends('layouts.admin')

@section('content')

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen User</h1>
            <p class="text-gray-500 text-sm">Kelola akun siswa dan guru</p>
        </div>
        <a href="{{ route('users.create') }}"
            class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg shadow-sm transition">
            + Tambah User
        </a>
    </div>

    <!-- ALERT -->
    @if(session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    <!-- TABLE -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-gray-700">

            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Nama</th>
                    <th class="px-6 py-4 text-left">Nomor Induk</th>
                    <th class="px-6 py-4 text-left">Email</th>
                    <th class="px-6 py-4 text-left">No HP</th>
                    <th class="px-6 py-4 text-left">Role</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-gray-800">{{ $user->name }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $user->nomor_induk ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $user->email }}
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        {{ $user->no_hp ?? '-' }}
                    </td>

                    <td class="px-6 py-4">
                        @php
                        $roleBadge = match($user->role) {
                        'admin' => 'bg-red-100 text-red-700',
                        'guru' => 'bg-blue-100 text-blue-700',
                        'siswa' => 'bg-green-100 text-green-700',
                        default => 'bg-gray-100 text-gray-600',
                        };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $roleBadge }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">

                            <a href="{{ route('users.edit', $user->id) }}"
                                class="px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition text-xs font-medium">
                                Edit
                            </a>

                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Yakin hapus user {{ $user->name }}?')"
                                    class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition text-xs font-medium">
                                    Hapus
                                </button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400 px-3 py-1.5">Akun kamu</span>
                            @endif

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-10 text-gray-500">
                        Belum ada data user
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>

@endsection