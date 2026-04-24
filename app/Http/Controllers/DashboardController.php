<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // Data untuk dashboard admin
        $totalBarang     = Item::count();
        $totalAset       = Item::aset()->count();
        $totalKonsumsi   = Item::konsumsi()->count();
        $stokKritis      = Item::stokKritis()->count();

        $totalPinjam     = Borrowing::where('status', 'dipinjam')->count();
        $totalPending    = Borrowing::where('status', 'pending')->count();
        $totalKembali    = Borrowing::where('status', 'dikembalikan')->count();

        $barangMasukHariIni = IncomingItem::whereDate('tanggal_masuk', today())->count();

        // 5 peminjaman terbaru yang pending — untuk notifikasi admin
        $peminjamanPending = Borrowing::with(['item', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
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
        ));
    }

    // Data untuk dashboard user
    $peminjamanSaya = Borrowing::with('item')
        ->where('id_user', auth()->id())
        ->latest()
        ->take(5)
        ->get();

    $riwayatAmbil = \App\Models\ItemUsage::with('item')
        ->where('id_user', auth()->id())
        ->latest()
        ->take(5)
        ->get();

    return view('user.dashboard', compact(
        'peminjamanSaya',
        'riwayatAmbil',
    ));
}
}