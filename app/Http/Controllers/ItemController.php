<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cabinet;
use App\Exports\ItemExport;
use Illuminate\Http\Request;
use App\Services\ItemService;
use App\Models\Room;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    protected $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    // ================= ADMIN =================

    public function index(Request $request)
    {
        $query = Item::with('cabinet.room')->latest();

        if ($request->search) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->jenis && $request->jenis !== 'semua') {
            $query->where('jenis_barang', $request->jenis);
        }

        $barangs = $query->paginate(15)->withQueryString();

        return view('admin.items.index', compact('barangs'));
    }

    public function export(Request $request)
    {
        $filename = 'barang-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new ItemExport($request), $filename);
    }

    public function create()
    {
        $lemaris = Cabinet::with('room')->get();
        $rooms   = Room::orderBy('nama_ruangan')->get();
        return view('admin.items.create', compact('lemaris', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'     => 'required|string|max:255',
            'kategori'        => 'required|string|max:255',
            'jenis_barang'    => 'required|in:aset,konsumsi',
            'merk'            => 'nullable|string|max:255',
            'hasil_perolehan' => 'nullable|in:pembelian,hibah,sumbangan,dana_bos',
            'id_ruangan'      => 'nullable|exists:rooms,id',
            'id_lemari'       => 'nullable|exists:cabinets,id',
            'stok_awal'       => 'required|integer|min:0',
            'batas_minimum'   => 'required|integer|min:0',
            'harga'           => 'required|integer|min:0',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // id_ruangan hanya untuk filter lemari di form, tidak disimpan ke items
            $data = $request->except([ '_token', '_method']);
            $this->service->store($data, $request->file('foto'));

            return redirect()->route('items.index')
                ->with('success', 'Barang berhasil ditambahkan');
        } catch (\Exception $e) {
             dd($e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $barang = Item::findOrFail($id);

        $request->validate([
            'nama_barang'   => 'required|string|max:255',
            'kategori'      => 'required|string|max:255',
            'jenis_barang'  => 'required|in:aset,konsumsi',
            'id_lemari'     => 'nullable|exists:cabinets,id',
            'batas_minimum' => 'required|integer|min:0',
            'harga'         => 'required|integer|min:0',
            'kondisi'       => 'nullable|in:Baik,Rusak Ringan,Rusak Sedang,Rusak Berat,Mati Total',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $data = $request->except(['_token', '_method']);
            $this->service->update($barang, $data, $request->file('foto'));

            return redirect()->route('items.index')
                ->with('success', 'Barang berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadQr($id)
    {
        $barang = Item::findOrFail($id);
        return view('admin.items.qr-print', compact('barang'));
    }

    public function destroy($id)
    {
        $barang = Item::findOrFail($id);
        $this->service->delete($barang);
        return back()->with('success', 'Barang berhasil dihapus');
    }

    // ================= USER =================

    public function userIndex(Request $request)
    {
        $query = Item::with('cabinet.room');

        if ($request->search) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        $barangs = $query->latest()->get();

        return view('user.items.index', compact('barangs'));
    }

    public function showUser($id)
    {
        $barang = Item::with('cabinet.room')->findOrFail($id);
        return view('user.items.show', compact('barang'));
    }

    // ================= SCAN =================

    public function scan($kode)
    {
        $barang = Item::with('cabinet.room')->where('kode_barang', $kode)->first();

        if (!$barang) {
            abort(404, 'Barang dengan kode ' . $kode . ' tidak ditemukan.');
        }

        return view('scan.detail', compact('barang'));
    }

    public function edit($id)
    {
        $barang  = Item::with('conditionLogs.admin')->findOrFail($id);
        $lemaris = Cabinet::with('room')->get();
        $rooms   = Room::orderBy('nama_ruangan')->get();
        return view('admin.items.edit', compact('barang', 'lemaris', 'rooms'));
    }
}