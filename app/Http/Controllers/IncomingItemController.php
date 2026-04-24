<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\IncomingItem;
use App\Services\ItemService;
use Illuminate\Http\Request;

class IncomingItemController extends Controller
{
    protected $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

            public function index()
        {
            $data = IncomingItem::with(['item', 'admin'])
                ->latest()
                ->paginate(15);

            return view('admin.incoming-items.index', compact('data'));
        }

        public function create()
        {
            $items = Item::orderBy('nama_barang')->get();

            return view('admin.incoming-items.create', compact('items'));
        }

        public function store(Request $request)
        {
            $validated = $request->validate([
                'id_barang'     => 'required|exists:items,id',
                'jumlah_masuk'  => 'required|integer|min:1',
                'tanggal_masuk' => 'required|date',
            ]);

            // Catat riwayat barang masuk
            IncomingItem::create([
                'id_barang'     => $validated['id_barang'],
                'id_admin'      => auth()->id(),
                'jumlah_masuk'  => $validated['jumlah_masuk'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
            ]);

            // Tambah stok otomatis lewat ItemService
            $item = Item::findOrFail($validated['id_barang']);
            $this->service->tambahStok($item, $validated['jumlah_masuk']);

            return redirect()->route('incoming-items.index')
                ->with('success', "Stok {$item->nama_barang} bertambah {$validated['jumlah_masuk']}.");
        }

}