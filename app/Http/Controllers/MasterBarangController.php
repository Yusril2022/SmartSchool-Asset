<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cabinet;
use Illuminate\Http\Request;
use App\Services\BarangService;

class MasterBarangController extends Controller
{
    protected $service;

    public function __construct(BarangService $service)
    {
        $this->service = $service;
    }

    // ================= ADMIN =================

    public function index()
    {
        $barangs = Item::with('cabinet.room')->latest()->get();

        return view('admin.master-barang.index', compact('barangs'));
    }

    public function create()
    {
        // dulu: Lemari
        // sekarang: Cabinet
        $lemaris = Cabinet::with('room')->get();

        return view('admin.master-barang.create', compact('lemaris'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'nama_barang'   => 'required',
            'kategori'      => 'required',
            'stok_awal'     => 'required|integer',
            'id_lemari'     => 'required|exists:cabinets,id', // 🔥 FIX
            'jenis_barang'  => 'required',
        ]);

        try {
            $this->service->store($request->all());

            return redirect()->route('master-barang.index')
                ->with('success', 'Barang berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $barang = Item::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required',
            'kategori'    => 'required',
        ]);

        try {
            $this->service->update($barang, $request->all());

            return redirect()->route('master-barang.index')
                ->with('success', 'Barang berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
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

        return view('user.barang.index', compact('barangs'));
    }

    public function showUser($id)
    {
        $barang = Item::with('cabinet.room')->findOrFail($id);

        return view('user.barang.show', compact('barang'));
    }

    // ================= SCAN =================



    public function scan($kode)
    {
        $barang = Item::where('kode_barang', $kode)->first();

        if (!$barang) {
            return back()->with('error', 'Barang tidak ditemukan');
        }

        return redirect()->route('peminjaman.create', $barang->id);
    }

    public function edit($id)
    {
        $barang = Item::findOrFail($id);
        $lemaris = Cabinet::with('room')->get();

        return view('admin.master-barang.edit', compact('barang', 'lemaris'));
    }
}
