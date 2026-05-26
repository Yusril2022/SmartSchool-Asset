<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-800">

    <div x-data="{ open: false, collapse: false }" class="flex h-screen overflow-hidden">

        {{-- OVERLAY MOBILE --}}
        <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 bg-black/40 z-20 md:hidden">
        </div>

        {{-- SIDEBAR --}}
        <aside :class="open ? 'translate-x-0' : '-translate-x-full'" :style="collapse ? 'width:72px' : 'width:260px'"
            class="fixed md:relative z-30 h-screen flex flex-col bg-white border-r border-gray-200 shadow-sm
                      transform transition-all duration-300 md:translate-x-0 overflow-y-auto">

            {{-- HEADER SIDEBAR --}}
            <div class="flex items-center justify-between p-4 border-b border-gray-200 shrink-0">
                <span x-show="!collapse" class="font-semibold text-gray-700 text-lg truncate">
                    Smart Assets
                </span>
                {{-- Tombol close di mobile --}}
                <button @click="open = false" class="md:hidden text-gray-400 hover:text-gray-600 text-xl ml-auto">
                    ✕
                </button>
                {{-- Tombol collapse di desktop --}}
                <button @click="collapse = !collapse" class="hidden md:block text-gray-500 hover:text-orange-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            {{-- MENU --}}
            <nav class="mt-2 flex-1 space-y-1 px-2 py-2">

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('dashboard') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">🏠</span>
                    <span x-show="!collapse" class="truncate">Dashboard</span>
                </a>

                @if(auth()->user()->role == 'admin')

                <a href="{{ route('rooms.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('rooms.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">🏢</span>
                    <span x-show="!collapse" class="truncate">Ruangan</span>
                </a>

                <a href="{{ route('cabinets.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('cabinets.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">🗄️</span>
                    <span x-show="!collapse" class="truncate">Lemari</span>
                </a>

                <a href="{{ route('items.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('items.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">📦</span>
                    <span x-show="!collapse" class="truncate">Barang</span>
                </a>

                <a href="{{ route('incoming-items.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('incoming-items.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">📥</span>
                    <span x-show="!collapse" class="truncate">Barang Masuk</span>
                </a>

                <a href="{{ route('admin.borrowings.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('admin.borrowings.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">📄</span>
                    <span x-show="!collapse" class="truncate">Peminjaman</span>
                </a>

                <a href="{{ route('admin.item-usages.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('admin.item-usages.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">📋</span>
                    <span x-show="!collapse" class="truncate">Pengambilan</span>
                </a>

                <a href="{{ route('documents.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('documents.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">📁</span>
                    <span x-show="!collapse" class="truncate">Dokumen</span>
                </a>

                <a href="{{ route('asset-position.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('asset-position.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">🗂️</span>
                    <span x-show="!collapse" class="truncate">Posisi Aset</span>
                </a>

                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('users.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">👥</span>
                    <span x-show="!collapse" class="truncate">Users</span>
                </a>

                @endif

            </nav>

            {{-- LOGOUT --}}
            <div class="p-3 border-t border-gray-200 shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 transition text-sm">
                        <span class="text-base shrink-0">🚪</span>
                        <span x-show="!collapse" class="truncate">Logout</span>
                    </button>
                </form>
            </div>

        </aside>

        {{-- MAIN --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- NAVBAR --}}
            <header
                class="h-16 flex items-center justify-between px-4 md:px-6 bg-white border-b border-gray-200 shrink-0">

                <div class="flex items-center gap-3">
                    {{-- Hamburger mobile --}}
                    <button @click="open = true" class="md:hidden text-gray-600 hover:text-orange-500 text-xl">
                        ☰
                    </button>
                    <h1 class="font-semibold text-gray-700 text-sm md:text-base">Admin Panel</h1>
                </div>

                <div class="flex items-center gap-3">

                    {{-- NOTIFIKASI --}}
                    <div x-data="{ notifOpen: false }" class="relative">

                        {{-- TOMBOL LONCENG --}}
                        <button @click="notifOpen = !notifOpen"
                            class="relative text-gray-400 hover:text-orange-500 transition">
                            <span class="text-xl">🔔</span>
                            @if($notifCount > 0)
                            <span
                                class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">
                                {{ $notifCount > 9 ? '9+' : $notifCount }}
                            </span>
                            @endif
                        </button>

                        {{-- DROPDOWN --}}
                        <div x-show="notifOpen" x-transition @click.outside="notifOpen = false"
                            class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-2xl shadow-lg z-50 overflow-hidden">

                            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                <span class="font-semibold text-gray-800 text-sm">Notifikasi</span>
                                @if($notifCount > 0)
                                <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">
                                    {{ $notifCount }} baru
                                </span>
                                @endif
                            </div>

                            <ul class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                @forelse($notifData as $notif)
                                <li>
                                    <a href="{{ $notif['url'] }}"
                                        class="flex items-start gap-3 px-4 py-3 hover:{{ $notif['bg'] }} transition">
                                        <span class="text-lg shrink-0 mt-0.5">{{ $notif['icon'] }}</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-700 leading-snug">{{ $notif['text'] }}</p>
                                            @if($notif['time'])
                                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $notif['time'] }}</p>
                                            @endif
                                        </div>
                                    </a>
                                </li>
                                @empty
                                <li class="px-4 py-6 text-center text-gray-400 text-sm">
                                    <div class="text-3xl mb-2">✅</div>
                                    Semua aman, tidak ada notifikasi
                                </li>
                                @endforelse
                            </ul>

                            @if($notifCount > 0)
                            <div class="px-4 py-2 border-t border-gray-100 text-center">
                                <a href="{{ route('admin.borrowings.index') }}"
                                    class="text-xs text-orange-500 hover:underline font-medium">
                                    Lihat semua peminjaman →
                                </a>
                            </div>
                            @endif

                        </div>
                    </div>

                    <span class="text-sm text-gray-600 hidden md:block">{{ auth()->user()->name }}</span>
                    <img src="https://i.pravatar.cc/40"
                        class="w-8 h-8 md:w-9 md:h-9 rounded-full border border-gray-200">
                </div>

            </header>

            {{-- CONTENT --}}
            <main class="flex-1 overflow-y-auto bg-gray-100 p-3 md:p-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-4 md:p-6 shadow-sm">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');

        forms.forEach(function(form) {

            // Skip form hapus — tidak perlu loading
            if (form.hasAttribute('data-no-loading')) return;

            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');

                if (submitBtn) {
                    submitBtn.dataset.originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block text-white" 
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" 
                                stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" 
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Memproses...
                `;
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                }
            });

        });
    });
    </script>

</body>

</html>