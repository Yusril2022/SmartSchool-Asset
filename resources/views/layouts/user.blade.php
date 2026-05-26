<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>User - Smart Assets</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">

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
                <h1 x-show="!collapse" class="text-lg font-semibold text-gray-700 truncate">
                    Smart Assets
                </h1>
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

                <a href="{{ route('items.user') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('items.user*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">📦</span>
                    <span x-show="!collapse" class="truncate">Daftar Barang</span>
                </a>

                <a href="{{ route('borrowings.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition text-sm
                   {{ request()->routeIs('borrowings.*') ? 'bg-orange-500 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}">
                    <span class="text-base shrink-0">📄</span>
                    <span x-show="!collapse" class="truncate">Peminjaman</span>
                </a>

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
            <nav class="h-16 flex items-center justify-between px-4 md:px-6 bg-white border-b border-gray-200 shrink-0">

                <div class="flex items-center gap-3">
                    <button @click="open = true" class="md:hidden text-gray-600 hover:text-orange-500 text-xl">
                        ☰
                    </button>
                    <h2 class="font-semibold text-gray-700 text-sm md:text-base">User Panel</h2>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600 hidden md:block">{{ auth()->user()->name }}</span>
                    <img src="https://i.pravatar.cc/40"
                        class="w-8 h-8 md:w-9 md:h-9 rounded-full border border-gray-200">
                </div>

            </nav>

            {{-- CONTENT --}}
            <main class="flex-1 overflow-y-auto bg-gray-100 p-3 md:p-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-4 md:p-6 shadow-sm">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

    @yield('scripts')

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