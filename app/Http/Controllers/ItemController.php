<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cabinet;
use Illuminate\Http\Request;
use App\Services\ItemService;

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
    
        // Filter search nama
        if ($request->search) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }
    
        // Filter jenis barang
        if ($request->jenis && $request->jenis !== 'semua') {
            $query->where('jenis_barang', $request->jenis);
        }
    
        $barangs = $query->paginate(15)->withQueryString();
    
        return view('admin.items.index', compact('barangs'));
    }

    public function create()
    {
        $lemaris = Cabinet::with('room')->get();

        return view('admin.items.create', compact('lemaris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'     => 'required|string|max:255',
            'kategori'        => 'required|string|max:255',
            'jenis_barang'    => 'required|in:aset,konsumsi',
            'merk'            => 'nullable|string|max:255',
            'hasil_perolehan' => 'nullable|in:pembelian,hibah,sumbangan,dana_bos',
            'id_lemari'       => 'required|exists:cabinets,id',
            'stok_awal'       => 'required|integer|min:0',
            'batas_minimum'   => 'required|integer|min:0',
            'harga'           => 'required|integer|min:0',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $this->service->store($request->all(), $request->file('foto'));

            return redirect()->route('items.index')
                ->with('success', 'Barang berhasil ditambahkan');
        } catch (\Exception $e) {
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
            'id_lemari'     => 'required|exists:cabinets,id',
            'batas_minimum' => 'required|integer|min:0',
            'harga'         => 'required|integer|min:0',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $this->service->update($barang, $request->all(), $request->file('foto'));

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

    /**
     * Dipanggil saat QR di-scan.
     * Route: GET /scan/{kode}  → PUBLIC (tidak perlu login)
     *
     * Menampilkan halaman detail barang.
     * Tombol aksi (pinjam / ambil) hanya muncul kalau user sudah login.
     */
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
        $barang = Item::findOrFail($id);
        $lemaris = Cabinet::with('room')->get();

        return view('admin.items.edit', compact('barang', 'lemaris'));
    }
}