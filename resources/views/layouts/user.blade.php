<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>User - Smart Assets</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-800">

    <div x-data="{ open:false, collapse:false }" class="flex h-screen">

        <!-- OVERLAY MOBILE -->
        <div x-show="open" @click="open=false" class="fixed inset-0 bg-black/30 z-20 md:hidden">
        </div>

        <!-- SIDEBAR -->
        <aside :class="open ? 'translate-x-0' : '-translate-x-full'" class="fixed md:relative z-30 h-screen transform transition-all duration-300 md:translate-x-0
               bg-white border-r border-gray-200 flex flex-col shadow-sm"
            :style="collapse ? 'width:80px' : 'width:260px'">

            <!-- MOBILE CLOSE -->
            <div class="flex justify-end p-3 md:hidden">
                <button @click="open=false" class="text-gray-500 text-xl">✕</button>
            </div>

            <!-- LOGO + TOGGLE -->
            <div class="flex items-center justify-between px-4 pb-4 border-b border-gray-200">

                <h1 x-show="!collapse" class="text-lg font-semibold text-gray-700">
                    Smart Assets
                </h1>

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

                <!-- Barang -->
                <a href="{{ route('items.user') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
               {{ request()->routeIs('items.user') 
               ? 'bg-orange-500 text-white shadow' 
               : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>📦</span>
                    <span x-show="!collapse">Daftar Barang</span>
                </a>

                <!-- Peminjaman -->
                <a href="{{ route('borrowings.index') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
               {{ request()->routeIs('borrowings.*') 
               ? 'bg-orange-500 text-white shadow' 
               : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>📄</span>
                    <span x-show="!collapse">Peminjaman</span>
                </a>

                <!-- Scan -->
                <a href="{{ route('scan.barang') }}" class="flex items-center gap-3 px-4 py-3 mx-2 rounded-lg transition
               {{ request()->routeIs('scan.*') 
               ? 'bg-orange-500 text-white shadow' 
               : 'text-gray-600 hover:bg-gray-100' }}">

                    <span>📷</span>
                    <span x-show="!collapse">Scan Barang</span>
                </a>

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
            <nav class="h-16 flex items-center justify-between px-6 bg-white border-b border-gray-200">

                <div class="flex items-center gap-4">

                    <!-- MOBILE -->
                    <button @click="open=true" class="md:hidden text-xl text-gray-600">
                        ☰
                    </button>

                    <h2 class="font-semibold text-gray-700">
                        User Panel
                    </h2>

                </div>

                <div class="flex items-center gap-4">

                    <span class="text-sm text-gray-600 hidden md:block">
                        {{ auth()->user()->name }}
                    </span>

                    <img src="https://i.pravatar.cc/40" class="w-9 h-9 rounded-full border border-gray-200">

                </div>

            </nav>

            <!-- CONTENT -->
            <main class="p-6 overflow-y-auto bg-gray-100">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    @yield('content')
                </div>
            </main>

        </div>

    </div>

</body>
@yield('scripts')

</html>