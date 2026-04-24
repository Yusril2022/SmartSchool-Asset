<?php 

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUsage;
use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemUsageController extends Controller
{

    public function __construct(protected ItemService $service) {}

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

    // Guard: pastikan barang ini konsumsi
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

    // Guard: pastikan barang konsumsi
    if ($item->jenis_barang !== 'konsumsi') {
        return back()->with('error', 'Barang ini bukan barang konsumsi.');
    }

    try {
        // Kurangi stok dulu — kalau stok tidak cukup, exception dilempar dari service
        $this->service->kurangiStok($item, $validated['jumlah_ambil']);

        // Catat riwayat pengambilan
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


}