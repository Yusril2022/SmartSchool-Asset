<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUsage;
use App\Services\ItemService;
use App\Exports\ItemUsageExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class ItemUsageController extends Controller
{
    public function __construct(protected ItemService $service) {}

    // =====================================================
    // USER LOGIN — riwayat pengambilan milik sendiri
    // =====================================================

    #[OA\Get(
        path: "/item-usages",
        summary: "Riwayat pengambilan konsumsi milik sendiri",
        description: "Menampilkan daftar pengambilan barang konsumsi yang dilakukan oleh user yang sedang login.",
        tags: ["Pengambilan Konsumsi"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman riwayat pengambilan"),
            new OA\Response(response: 401, description: "Belum login"),
        ]
    )]
    public function index()
    {
        $data = ItemUsage::with('item')
            ->where('id_user', auth()->id())
            ->latest()
            ->paginate(10);

        return view('user.item-usages.index', compact('data'));
    }

    #[OA\Get(
        path: "/admin/item-usages",
        summary: "Daftar semua pengambilan konsumsi (admin)",
        description: "Menampilkan semua pengambilan konsumsi dengan filter tanggal, nama pengambil, dan jenis barang. Hanya untuk admin.",
        tags: ["Pengambilan Konsumsi"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "dari", in: "query", required: false, description: "Tanggal mulai (Y-m-d)", schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "sampai", in: "query", required: false, description: "Tanggal akhir (Y-m-d)", schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "nama", in: "query", required: false, description: "Nama pengambil", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "barang", in: "query", required: false, description: "ID barang", schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar pengambilan admin"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function adminIndex(Request $request)
    {
        $query = ItemUsage::with(['item', 'user'])->latest();

        if ($request->dari) {
            $query->whereDate('tanggal_ambil', '>=', $request->dari);
        }
        if ($request->sampai) {
            $query->whereDate('tanggal_ambil', '<=', $request->sampai);
        }
        if ($request->nama) {
            $query->where('nama_pengambil', 'like', '%' . $request->nama . '%');
        }
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

    #[OA\Get(
        path: "/admin/item-usages/export",
        summary: "Export pengambilan konsumsi ke Excel",
        description: "Download file Excel berisi data pengambilan konsumsi. Mendukung filter yang sama dengan halaman daftar. Hanya untuk admin.",
        tags: ["Pengambilan Konsumsi"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "dari", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "sampai", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "nama", in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "barang", in: "query", required: false, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "File Excel (.xlsx)"),
        ]
    )]
    public function export(Request $request)
    {
        $filename = 'pengambilan-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new ItemUsageExport($request), $filename);
    }

    #[OA\Get(
        path: "/item-usages/{id}",
        summary: "Form pengambilan barang konsumsi (user login)",
        description: "Menampilkan form pengambilan barang konsumsi. Barang aset tidak bisa diambil lewat sini.",
        tags: ["Pengambilan Konsumsi"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID barang konsumsi",
                schema: new OA\Schema(type: "integer", example: 2)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman form pengambilan"),
            new OA\Response(response: 302, description: "Redirect dengan error jika barang bukan konsumsi"),
            new OA\Response(response: 404, description: "Barang tidak ditemukan"),
        ]
    )]
    public function create($id)
    {
        $item = Item::findOrFail($id);

        if ($item->jenis_barang !== 'konsumsi') {
            return redirect()->back()
                ->with('error', 'Barang aset tidak bisa diambil langsung, gunakan fitur peminjaman.');
        }

        return view('user.item-usages.create', compact('item'));
    }

    #[OA\Post(
        path: "/item-usages",
        summary: "Simpan pengambilan konsumsi (user login)",
        description: "Menyimpan pengambilan barang konsumsi oleh user yang sedang login dan otomatis mengurangi stok.",
        tags: ["Pengambilan Konsumsi"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id_barang", "jumlah_ambil"],
                properties: [
                    new OA\Property(property: "id_barang", type: "integer", example: 2),
                    new OA\Property(property: "jumlah_ambil", type: "integer", example: 2, description: "Jumlah yang diambil (min: 1)"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke riwayat pengambilan jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
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
                'id_barang'      => $item->id,
                'id_user'        => auth()->id(),
                'nama_pengambil' => auth()->user()->name,
                'sebagai'        => $validated['sebagai'],
                'jumlah_ambil'   => $validated['jumlah_ambil'],
                'tanggal_ambil'  => now(),
            ]);

            return redirect()->route('item-usages.index')
                ->with('success', "Berhasil mengambil {$validated['jumlah_ambil']} {$item->nama_barang}.");

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // =====================================================
    // PUBLIK — konsumsi tanpa login (scan QR)
    // =====================================================

    #[OA\Get(
        path: "/ambil",
        summary: "Form pengambilan konsumsi publik (tanpa login)",
        description: "Menampilkan form pengambilan barang konsumsi yang dapat diakses tanpa login. Dipanggil setelah scan QR barang.",
        tags: ["Pengambilan Konsumsi"],
        responses: [
            new OA\Response(response: 200, description: "Halaman form pengambilan publik"),
        ]
    )]
    public function formPublic()
    {
        $items = Item::konsumsi()
            ->where('stok_total', '>', 0)
            ->orderBy('nama_barang')
            ->get();

        return view('public.consumption-form', compact('items'));
    }

    #[OA\Post(
        path: "/ambil",
        summary: "Proses pengambilan konsumsi publik (tanpa login)",
        description: "Memproses pengambilan barang konsumsi tanpa memerlukan login. Bisa mengambil lebih dari satu barang sekaligus dalam satu transaksi.",
        tags: ["Pengambilan Konsumsi"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nama_pengambil", "sebagai", "items"],
                properties: [
                    new OA\Property(property: "nama_pengambil", type: "string", example: "Ahmad Fauzi"),
                    new OA\Property(property: "sebagai", type: "string", enum: ["murid", "pegawai"], example: "murid"),
                    new OA\Property(
                        property: "items",
                        type: "array",
                        description: "Minimal 1 barang",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id_barang", type: "integer", example: 2),
                                new OA\Property(property: "jumlah_ambil", type: "integer", example: 1),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke halaman sukses jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
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

    #[OA\Get(
        path: "/ambil/sukses",
        summary: "Halaman sukses setelah pengambilan publik",
        description: "Ditampilkan setelah pengambilan konsumsi publik berhasil diproses.",
        tags: ["Pengambilan Konsumsi"],
        responses: [
            new OA\Response(response: 200, description: "Halaman sukses"),
        ]
    )]
    public function sukses()
    {
        return view('public.success');
    }
}