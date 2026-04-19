@extends('layouts.admin')

@section('content')

<div class="p-6 space-y-6">

    <!-- HEADER -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-800">
            Dashboard Admin
        </h1>
        <p class="text-gray-500 text-sm">
            Overview of your inventory system
        </p>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Total Barang</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-2">12</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Total Stok</p>
            <h2 class="text-3xl font-bold text-gray-800 mt-2">120</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Barang Dipinjam</p>
            <h2 class="text-3xl font-bold text-orange-500 mt-2">5</h2>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
            <p class="text-gray-500 text-sm">Barang Dikembalikan</p>
            <h2 class="text-3xl font-bold text-green-500 mt-2">7</h2>
        </div>

    </div>

    <!-- CHART + ACTIVITY -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- CHART -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-gray-800 font-semibold mb-4">
                Statistik Peminjaman
            </h3>

            <canvas id="chartPeminjaman"></canvas>
        </div>

        <!-- ACTIVITY -->
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h3 class="text-gray-800 font-semibold mb-4">
                Aktivitas Terbaru
            </h3>

            <ul class="space-y-3 text-sm text-gray-600">
                <li>📦 Laptop dipinjam</li>
                <li>✅ Proyektor dikembalikan</li>
                <li>➕ Mouse ditambahkan</li>
                <li>📦 Keyboard dipinjam</li>
            </ul>
        </div>

    </div>

</div>

<!-- CHART SCRIPT -->
<script>
    const ctx = document.getElementById('chartPeminjaman');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'],
            datasets: [{
                label: 'Peminjaman',
                data: [3, 5, 2, 8, 6],
                borderColor: '#f97316', // ORANGE
                backgroundColor: 'rgba(249,115,22,0.15)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            plugins: {
                legend: {
                    labels: {
                        color: '#374151'
                    }
                }
            },
            scales: {
                x: {
                    ticks: { color: '#6b7280' },
                    grid: { color: '#e5e7eb' }
                },
                y: {
                    ticks: { color: '#6b7280' },
                    grid: { color: '#e5e7eb' }
                }
            }
        }
    });
</script>

@endsection