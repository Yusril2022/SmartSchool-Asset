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

    <div x-data="{ open:false, collapse:false }" class="flex h-screen">

        <!-- OVERLAY -->
        <div x-show="open" @click="open=false" class="fixed inset-0 bg-black/30 z-20 md:hidden"></div>

        <!-- SIDEBAR -->
        <aside :class="open ? 'translate-x-0' : '-translate-x-full'" class="fixed md:relative z-30 h-screen transform transition-all duration-300 md:translate-x-0
               bg-white border-r border-gray-200 flex flex-col shadow-sm"
            :style="collapse ? 'width:80px' : 'width:260px'">

            <!-- HEADER -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200">

                <span x-show="!collapse" class="font-semibold text-gray-700 text-lg">
                    Smart Assets
                </span>

                <!-- TOGGLE -->
                <button @click="collapse = !collapse" class="text-gray-500 hover:text-orange-500">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                </button>

            </div>

            <!-- MENU -->
            <nav class="mt-4 space-y-2 flex-1">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('dashboard') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>🏠</span>
                    <span x-show="!collapse">Dashboard</span>
                </a>

                @if(auth()->user()->role == 'admin')

                <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('rooms.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>🏢</span>
                    <span x-show="!collapse">Ruangan</span>
                </a>

                <a href="{{ route('cabinets.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('cabinets.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>🗄️</span>
                    <span x-show="!collapse">Lemari</span>
                </a>

                <a href="{{ route('items.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('items.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>📦</span>
                    <span x-show="!collapse">Barang</span>
                </a>

                <a href="{{ route('incoming-items.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('incoming-items.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>📥</span>
                    <span x-show="!collapse">Barang Masuk</span>
                </a>

                <a href="{{ route('admin.borrowings.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('admin.borrowings.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>📄</span>
                    <span x-show="!collapse">Peminjaman</span>
                </a>

                <a href="{{ route('admin.item-usages.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('admin.item-usages.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>📋</span>
                    <span x-show="!collapse">Pengambilan</span>
                </a>

                <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('documents.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>📁</span>
                    <span x-show="!collapse">Dokumen</span>
                </a>

                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
                {{ request()->routeIs('users.*') 
                ? 'bg-orange-500 text-white shadow' 
                : 'text-gray-600 hover:bg-gray-100' }}">
                    <span>👥</span>
                    <span x-show="!collapse">Users</span>
                </a>



                @endif

            </nav>

            <!-- LOGOUT -->
            <div class="p-4 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 transition">

                        <span>🚪</span>
                        <span x-show="!collapse">Logout</span>

                    </button>
                </form>
            </div>

        </aside>

        <!-- MAIN -->
        <div class="flex-1 flex flex-col">

            <!-- NAVBAR -->
            <header class="h-16 flex items-center justify-between px-6 bg-white border-b border-gray-200">

                <div class="flex items-center gap-4">

                    <button @click="open=true" class="md:hidden text-xl text-gray-600">
                        ☰
                    </button>

                    <h1 class="font-semibold text-gray-700">
                        Dashboard
                    </h1>

                </div>

                <div class="flex items-center gap-4">

                    <input type="text" placeholder="Search..."
                        class="hidden md:block px-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-400">

                    <button class="text-gray-500 hover:text-orange-500">
                        🔔
                    </button>

                    <img src="https://i.pravatar.cc/40" class="w-9 h-9 rounded-full border border-gray-200">

                </div>

            </header>

            <!-- CONTENT -->
            <main class="p-6 overflow-y-auto bg-gray-100">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

</body>

</html>