<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Item;
use Illuminate\Http\Request;

class AssetPositionController extends Controller
{
    // =========================================================
    // FORM PILIH RUANGAN
    // =========================================================
    public function index()
    {
        $rooms = Room::with(['cabinets.items' => function ($q) {
            $q->where('jenis_barang', 'aset');
        }])->get();

        return view('admin.reports.asset-position', compact('rooms'));
    }

    // =========================================================
    // HALAMAN PRINT
    // =========================================================
    public function print(Request $request)
    {
        $request->validate([
            'room_ids' => 'nullable|array',
            'room_ids.*' => 'exists:rooms,id',
        ]);

        // Jika tidak pilih ruangan spesifik, ambil semua
        $query = Room::with(['cabinets' => function ($q) {
            $q->with(['items' => function ($q2) {
                $q2->where('jenis_barang', 'aset')
                   ->orderBy('nama_barang');
            }]);
        }]);

        if ($request->filled('room_ids')) {
            $query->whereIn('id', $request->room_ids);
        }

        $rooms = $query->orderBy('nama_ruangan')->get();

        // Filter hanya ruangan yang punya aset
        $rooms = $rooms->filter(function ($room) {
            return $room->cabinets->some(function ($cabinet) {
                return $cabinet->items->isNotEmpty();
            });
        });

        return view('admin.reports.asset-position-print', compact('rooms'));
    }
}