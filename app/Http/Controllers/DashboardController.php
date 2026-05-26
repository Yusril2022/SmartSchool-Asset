<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ItemUsage;
use App\Models\Item;
use App\Models\Borrowing;
use App\Models\IncomingItem;

class DashboardController extends Controller
{
// SEBELUM
// SESUDAH
public function index()
{
    if (auth()->user()->role === 'admin') {

        $totalBarang        = Item::count();
        $totalAset          = Item::aset()->count();
        $totalKonsumsi      = Item::konsumsi()->count();
        $stokKritis         = Item::stokKritis()->count();
        $totalPinjam        = Borrowing::where('status', 'dipinjam')->count();
        $totalPending       = Borrowing::where('status', 'pending')->count();
        $totalKembali       = Borrowing::where('status', 'dikembalikan')->count();
        $barangMasukHariIni = IncomingItem::whereDate('tanggal_masuk', today())->count();

        $peminjamanPending = Borrowing::with(['item', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $chartData = Borrowing::selectRaw('DATE(created_at) as tanggal, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $chartLabels = $chartData->pluck('tanggal')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))
            ->toArray();

        $chartValues = $chartData->pluck('total')->toArray();

        // ── TOP 5 BARANG PALING SERING DIPINJAM (aset) ──
        $topDipinjam = \App\Models\Borrowing::selectRaw('id_barang, COUNT(*) as total_pinjam')
            ->whereIn('status', ['dipinjam', 'dikembalikan'])
            ->groupBy('id_barang')
            ->orderByDesc('total_pinjam')
            ->take(5)
            ->with('item')
            ->get();

        // ── TOP 5 BARANG PALING SERING DIAMBIL (konsumsi) ──
        $topDiambil = \App\Models\ItemUsage::selectRaw('id_barang, COUNT(*) as total_ambil, SUM(jumlah_ambil) as total_jumlah')
            ->groupBy('id_barang')
            ->orderByDesc('total_ambil')
            ->take(5)
            ->with('item')
            ->get();

        // ── PREDIKSI KEBUTUHAN BULAN DEPAN ──
        // Ambil rata-rata pengambilan per bulan selama 3 bulan terakhir per barang
        $prediksi = \App\Models\ItemUsage::selectRaw(
                'id_barang,
                 SUM(jumlah_ambil) as total_3bulan,
                 ROUND(SUM(jumlah_ambil) / 3.0) as prediksi_bulan_depan'
            )
            ->where('tanggal_ambil', '>=', now()->subMonths(3))
            ->groupBy('id_barang')
            ->orderByDesc('prediksi_bulan_depan')
            ->take(5)
            ->with('item')
            ->get();

        return view('admin.dashboard', compact(
            'totalBarang',
            'totalAset',
            'totalKonsumsi',
            'stokKritis',
            'totalPinjam',
            'totalPending',
            'totalKembali',
            'barangMasukHariIni',
            'peminjamanPending',
            'chartLabels',
            'chartValues',
            'topDipinjam',
            'topDiambil',
            'prediksi',
        ));
    }

    // User biasa
    $peminjamanSaya = Borrowing::with('item')
        ->where('id_user', auth()->id())
        ->latest()
        ->take(5)
        ->get();

    $riwayatAmbil = ItemUsage::with('item')
        ->where('id_user', auth()->id())
        ->latest()
        ->take(5)
        ->get();

    $totalPinjamSaya    = Borrowing::where('id_user', auth()->id())
                        ->where('status', 'dipinjam')->count();
    $totalKembaliSaya   = Borrowing::where('id_user', auth()->id())
                        ->where('status', 'dikembalikan')->count();
    $totalPendingSaya   = Borrowing::where('id_user', auth()->id())
                        ->where('status', 'pending')->count();

    return view('user.dashboard', compact(
        'peminjamanSaya',
        'riwayatAmbil',
        'totalPinjamSaya',
        'totalKembaliSaya',
        'totalPendingSaya',
    ));
}

}