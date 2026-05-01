<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUsage;
use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemUsageController extends Controller
{
    public function __construct(protected ItemService $service) {}

    // =====================================================
    // USER LOGIN — riwayat pengambilan milik sendiri
    // =====================================================

    public function index()
    {
        $data = ItemUsage::with('item')
            ->where('id_user', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.item-usages.index', compact('data'));
    }

    public function create($id)
    {
        $item = Item::findOrFail($id);

        if ($item->jenis_barang !== 'konsumsi') {
            return redirect()->back()
                ->with('error', 'Barang aset tidak bisa diambil langsung, gunakan fitur peminjaman.');
        }

        return view('user.item-usages.create', compact('item'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang'    => 'required|exists:items,id',
            'jumlah_ambil' => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($validated['id_barang']);

        if ($item->jenis_barang !== 'konsumsi') {
            return back()->with('error', 'Barang ini bukan barang konsumsi.');
        }

        try {
            $this->service->kurangiStok($item, $validated['jumlah_ambil']);

            ItemUsage::create([
                'id_barang'     => $item->id,
                'id_user'       => auth()->id(),
                'jumlah_ambil'  => $validated['jumlah_ambil'],
                'tanggal_ambil' => now(),
            ]);

            return redirect()->route('item-usages.index')
                ->with('success', "Berhasil mengambil {$validated['jumlah_ambil']} {$item->nama_barang}.");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // =====================================================
    // PUBLIK — konsumsi tanpa login (scan QR langsung)
    // =====================================================

    /**
     * Form pengambilan publik — dipanggil dari halaman scan QR barang konsumsi.
     * Route: GET /ambil/{kode}
     */
    public function formPublic($kode)
    {
        $item = Item::where('kode_barang', $kode)->first();

        if (!$item) {
            abort(404, 'Barang tidak ditemukan.');
        }

        if ($item->jenis_barang !== 'konsumsi') {
            abort(403, 'Barang ini adalah aset. Gunakan fitur peminjaman.');
        }

        return view('scan.consumption-form', compact('item'));
    }

    /**
     * Proses pengambilan publik — tidak butuh login.
     * Route: POST /ambil
     */
    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'id_barang'      => 'required|exists:items,id',
            'nama_pengambil' => 'required|string|max:255',
            'jumlah_ambil'   => 'required|integer|min:1',
        ], [
            'nama_pengambil.required' => 'Nama pengambil wajib diisi.',
            'jumlah_ambil.required'   => 'Jumlah wajib diisi.',
            'jumlah_ambil.min'        => 'Jumlah minimal 1.',
        ]);

        $item = Item::findOrFail($validated['id_barang']);

        if ($item->jenis_barang !== 'konsumsi') {
            return back()->with('error', 'Barang ini bukan barang konsumsi.');
        }

        try {
            $this->service->kurangiStok($item, $validated['jumlah_ambil']);

            ItemUsage::create([
                'id_barang'      => $item->id,
                'id_user'        => null,                        // tidak login
                'nama_pengambil' => $validated['nama_pengambil'],
                'jumlah_ambil'   => $validated['jumlah_ambil'],
                'tanggal_ambil'  => now(),
            ]);

            return redirect()->route('ambil.sukses', $item->kode_barang);

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman sukses setelah pengambilan publik.
     * Route: GET /ambil/{kode}/sukses
     */
    public function sukses($kode)
    {
        $item = Item::where('kode_barang', $kode)->firstOrFail();

        return view('scan.sucses', compact('item'));
    }
}