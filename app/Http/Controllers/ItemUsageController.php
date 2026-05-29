<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUsage;
use App\Services\ItemService;
use App\Exports\ItemUsageExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

    public function adminIndex(Request $request)
    {
        $query = ItemUsage::with(['item', 'user'])->latest();

        // Filter tanggal
        if ($request->dari) {
            $query->whereDate('tanggal_ambil', '>=', $request->dari);
        }
        if ($request->sampai) {
            $query->whereDate('tanggal_ambil', '<=', $request->sampai);
        }

        // Filter nama pengambil
        if ($request->nama) {
            $query->where('nama_pengambil', 'like', '%' . $request->nama . '%');
        }

        // Filter barang
        if ($request->barang) {
            $query->where('id_barang', $request->barang);
        }

        $data         = $query->paginate(15)->withQueryString();
        $totalDiambil = $query->sum('jumlah_ambil');
        $totalMurid   = $query->where('sebagai', 'murid')->count();
        $totalPegawai = $query->where('sebagai', 'pegawai')->count();
        $items        = \App\Models\Item::konsumsi()->orderBy('nama_barang')->get();

        return view('admin.item-usages.index', compact(
            'data',
            'totalDiambil',
            'totalMurid',
            'totalPegawai',
            'items',
        ));
    }

    public function export(Request $request)
    {
        $filename = 'pengambilan-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new ItemUsageExport($request), $filename);
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
                'nama_pengambil' => auth()->user()->name,
                'sebagai'       => $validated['sebagai'],
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
public function formPublic()
{
    $items = Item::konsumsi()
        ->where('stok_total', '>', 0)
        ->orderBy('nama_barang')
        ->get();

    return view('public.consumption-form', compact('items'));
}

    /**
     * Proses pengambilan publik — tidak butuh login.
     * Route: POST /ambil
     */
    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'nama_pengambil'          => 'required|string|max:255',
            'sebagai'                 => 'required|in:murid,pegawai',
            'items'                   => 'required|array|min:1',
            'items.*.id_barang'       => 'required|exists:items,id',
            'items.*.jumlah_ambil'    => 'required|integer|min:1',
        ], [
            'nama_pengambil.required' => 'Nama pengambil wajib diisi.',
            'sebagai.required'        => 'Kolom sebagai wajib diisi.',
            'items.required'          => 'Minimal 1 barang harus dipilih.',
            'items.*.id_barang.required' => 'Pilih barang di semua baris.',
            'items.*.jumlah_ambil.min'   => 'Jumlah minimal 1.',
        ]);

        $sessionId = (string) \Illuminate\Support\Str::uuid();

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $sessionId) {
                foreach ($validated['items'] as $row) {
                    $item = Item::findOrFail($row['id_barang']);

                    if ($item->jenis_barang !== 'konsumsi') {
                        throw new \Exception("{$item->nama_barang} bukan barang konsumsi.");
                    }

                    $this->service->kurangiStok($item, $row['jumlah_ambil']);

                    ItemUsage::create([
                        'session_id'     => $sessionId,
                        'id_barang'      => $item->id,
                        'id_user'        => null,
                        'nama_pengambil' => $validated['nama_pengambil'],
                        'sebagai'        => $validated['sebagai'],
                        'jumlah_ambil'   => $row['jumlah_ambil'],
                        'tanggal_ambil'  => now(),
                    ]);
                }
            });

            return redirect()->route('ambil.sukses');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Halaman sukses setelah pengambilan publik.
     * Route: GET /ambil/{kode}/sukses
     */
    public function sukses()
    {
        return view('public.success');
    }
}