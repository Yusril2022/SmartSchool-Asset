<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\IncomingItem;
use App\Services\ItemService;
use App\Exports\IncomingItemExport;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class IncomingItemController extends Controller
{
    protected $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: "/incoming-items",
        summary: "Daftar barang masuk",
        description: "Menampilkan riwayat semua pencatatan barang masuk dengan filter nama barang dan rentang tanggal. Hanya untuk admin.",
        tags: ["Barang Masuk"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, description: "Nama barang", schema: new OA\Schema(type: "string", example: "Spidol")),
            new OA\Parameter(name: "dari", in: "query", required: false, description: "Tanggal mulai (Y-m-d)", schema: new OA\Schema(type: "string", format: "date", example: "2025-05-01")),
            new OA\Parameter(name: "sampai", in: "query", required: false, description: "Tanggal akhir (Y-m-d)", schema: new OA\Schema(type: "string", format: "date", example: "2025-05-31")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar barang masuk"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index(Request $request)
    {
        $query = IncomingItem::with(['item', 'admin'])->latest();

        if ($request->search) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->dari) {
            $query->whereDate('tanggal_masuk', '>=', $request->dari);
        }

        if ($request->sampai) {
            $query->whereDate('tanggal_masuk', '<=', $request->sampai);
        }

        $data = $query->paginate(15)->withQueryString();

        return view('admin.incoming-items.index', compact('data'));
    }

    #[OA\Get(
        path: "/incoming-items/export",
        summary: "Export barang masuk ke Excel",
        description: "Download file Excel berisi data barang masuk. Mendukung filter yang sama dengan halaman daftar. Hanya untuk admin.",
        tags: ["Barang Masuk"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "dari", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "sampai", in: "query", required: false, schema: new OA\Schema(type: "string", format: "date")),
        ],
        responses: [
            new OA\Response(response: 200, description: "File Excel (.xlsx)"),
        ]
    )]
    public function export(Request $request)
    {
        $filename = 'barang-masuk-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new IncomingItemExport($request), $filename);
    }

    #[OA\Get(
        path: "/incoming-items/create",
        summary: "Form pencatatan barang masuk",
        description: "Menampilkan form untuk mencatat barang masuk baru. Hanya untuk admin.",
        tags: ["Barang Masuk"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman form barang masuk"),
        ]
    )]
    public function create(): View
    {
        $items = Item::orderBy('nama_barang')->get();
        return view('admin.incoming-items.create', compact('items'));
    }

    #[OA\Post(
        path: "/incoming-items",
        summary: "Catat barang masuk",
        description: "Menyimpan pencatatan barang masuk dan otomatis menambah stok barang yang bersangkutan.",
        tags: ["Barang Masuk"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id_barang", "jumlah_masuk", "tanggal_masuk"],
                properties: [
                    new OA\Property(property: "id_barang", type: "integer", example: 3, description: "ID barang yang masuk"),
                    new OA\Property(property: "jumlah_masuk", type: "integer", example: 50, description: "Jumlah barang yang masuk (min: 1)"),
                    new OA\Property(property: "tanggal_masuk", type: "string", format: "date", example: "2025-05-30", description: "Tanggal barang masuk"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar barang masuk jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang'     => 'required|exists:items,id',
            'jumlah_masuk'  => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
        ]);

        IncomingItem::create([
            'id_barang'     => $validated['id_barang'],
            'id_admin'      => auth()->id(),
            'jumlah_masuk'  => $validated['jumlah_masuk'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
        ]);

        $item = Item::findOrFail($validated['id_barang']);
        $this->service->tambahStok($item, $validated['jumlah_masuk']);

        return redirect()->route('incoming-items.index')
            ->with('success', "Stok {$item->nama_barang} bertambah {$validated['jumlah_masuk']}.");
    }
}