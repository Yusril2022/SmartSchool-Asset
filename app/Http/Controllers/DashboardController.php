<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterBarang;
use App\Models\TransaksiPeminjaman;

class DashboardController extends Controller
{
    public function index()
    {
        
           if (auth()->user()->role == 'admin') {
        return view('admin.dashboard');
    }

    return view('user.dashboard');
    

        // $totalBarang = MasterBarang::count();

        // $totalStok = MasterBarang::sum('stok_total');

        // $totalPinjam = TransaksiPeminjaman::where('status', 'dipinjam')->count();

        // $totalKembali = TransaksiPeminjaman::where('status', 'dikembalikan')->count();

        // return view('dashboard', compact(
        //     'totalBarang',
        //     'totalStok',
        //     'totalPinjam',
        //     'totalKembali'
        // ));
    }
}
