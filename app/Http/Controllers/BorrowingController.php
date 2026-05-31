<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Exports\BorrowingExport;
use Illuminate\Http\Request;
use App\Services\BorrowingService;
use App\Services\DocumentService;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class BorrowingController extends Controller
{
    public function __construct(
        protected BorrowingService $service,
        protected DocumentService $documentService,
    ) {}

    // =========================================================
    // DOWNLOAD BERITA ACARA
    // =========================================================

    #[OA\Get(
        path: "/admin/borrowings/{id}/berita-acara",
        summary: "Download berita acara peminjaman (PDF)",
        description: "Generate dan download berita acara peminjaman untuk barang bernilai di atas Rp10.000.000. Hanya untuk admin.",
        tags: ["Peminjaman"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID peminjaman",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "File PDF berita acara"),
            new OA\Response(response: 302, description: "Redirect dengan error jika harga barang <= 10 juta"),
            new OA\Response(response: 404, description: "Peminjaman tidak ditemukan"),
        ]
    )]
    public function downloadBeritaAcara($id)
    {
        $borrowing = Borrowing::with(['item.cabinet.room', 'user', 'admin'])
            ->findOrFail($id);

        if ($borrowing->item->harga <= 10_000_000) {
            return back()->with('error', 'Berita acara hanya untuk barang di atas 10 juta.');
        }

        return $this->documentService->streamBeritaAcara($borrowing);
    }

    // =========================================================
    // EXPORT EXCEL
    // =========================================================

    #[OA\Get(
        path: "/admin/borrowings/export",
        summary: "Export data peminjaman ke Excel",
        description: "Download file Excel berisi data peminjaman. Mendukung filter yang sama dengan halaman daftar peminjaman. Hanya untuk admin.",
        tags: ["Peminjaman"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "status", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["semua", "pending", "dipinjam", "dikembalikan", "ditolak", "terlambat"])),
            new OA\Parameter(name: "search", in: "query", required: false, description: "Nama peminjam", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "dari", in: "query", required: false, description: "Tanggal mulai (Y-m-d)", schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "sampai", in: "query", required: false, description: "Tanggal akhir (Y-m-d)", schema: new OA\Schema(type: "string", format: "date")),
        ],
        responses: [
            new OA\Response(response: 200, description: "File Excel (.xlsx)"),
        ]
    )]
    public function export(Request $request)
    {
        $filename = 'peminjaman-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new BorrowingExport($request), $filename);
    }

    // =========================================================
    // LIST
    // =========================================================

    #[OA\Get(
        path: "/admin/borrowings",
        summary: "Daftar semua peminjaman (admin)",
        description: "Menampilkan semua peminjaman dengan filter status, pencarian nama peminjam, dan rentang tanggal. Hanya untuk admin.",
        tags: ["Peminjaman"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "status", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["semua", "pending", "dipinjam", "dikembalikan", "ditolak", "terlambat"])),
            new OA\Parameter(name: "search", in: "query", required: false, description: "Nama peminjam", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "dari", in: "query", required: false, description: "Tanggal mulai (Y-m-d)", schema: new OA\Schema(type: "string", format: "date")),
            new OA\Parameter(name: "sampai", in: "query", required: false, description: "Tanggal akhir (Y-m-d)", schema: new OA\Schema(type: "string", format: "date")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar peminjaman"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            $query = Borrowing::with(['item', 'user'])->latest();

            if (request('status') && request('status') !== 'semua') {
                if (request('status') === 'terlambat') {
                    $query->where('status', 'dipinjam')
                        ->whereNotNull('tanggal_kembali')
                        ->where('tanggal_kembali', '<', now());
                } else {
                    $query->where('status', request('status'));
                }
            }

            if (request('search')) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . request('search') . '%'));
            }
            if (request('dari')) {
                $query->whereDate('tanggal_peminjaman', '>=', request('dari'));
            }
            if (request('sampai')) {
                $query->whereDate('tanggal_peminjaman', '<=', request('sampai'));
            }

            $data = $query->paginate(15);
            return view('admin.borrowings.index', compact('data'));
        }
    }

    // =========================================================
    // FORM PEMINJAMAN
    // =========================================================

    #[OA\Get(
        path: "/borrowings/create/{id}",
        summary: "Form pengajuan peminjaman barang aset",
        description: "Menampilkan form peminjaman untuk barang aset tertentu. Hanya untuk siswa dan guru. Barang konsumsi tidak bisa dipinjam lewat sini.",
        tags: ["Peminjaman"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID barang yang ingin dipinjam",
                schema: new OA\Schema(type: "integer", example: 5)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman form peminjaman"),
            new OA\Response(response: 302, description: "Redirect dengan error jika barang adalah konsumsi"),
            new OA\Response(response: 404, description: "Barang tidak ditemukan"),
        ]
    )]
    public function create($id)
    {
        $item = Item::findOrFail($id);

        if ($item->jenis_barang !== 'aset') {
            return redirect()->back()->with('error', 'Barang konsumsi tidak bisa dipinjam.');
        }

        return view('user.borrowings.create', compact('item'));
    }

    // =========================================================
    // STORE — simpan pengajuan peminjaman
    // =========================================================

    #[OA\Post(
        path: "/borrowings",
        summary: "Ajukan peminjaman barang aset",
        description: "Menyimpan pengajuan peminjaman. Status awal adalah 'pending' menunggu persetujuan admin. Untuk barang <= Rp10 juta, tanggal kembali wajib diisi.",
        tags: ["Peminjaman"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["id_barang", "jumlah_pinjam", "tujuan_pinjam"],
                properties: [
                    new OA\Property(property: "id_barang", type: "integer", example: 5, description: "ID barang yang dipinjam"),
                    new OA\Property(property: "jumlah_pinjam", type: "integer", example: 1, description: "Jumlah barang yang dipinjam (min: 1)"),
                    new OA\Property(property: "tujuan_pinjam", type: "string", example: "Digunakan untuk presentasi kelas", description: "Tujuan peminjaman"),
                    new OA\Property(property: "tanggal_kembali", type: "string", format: "date", example: "2025-06-01", description: "Wajib diisi jika harga barang <= Rp10 juta"),
                    new OA\Property(property: "jam_kembali", type: "string", example: "14:00", description: "Opsional, format HH:MM"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar peminjaman jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function store(Request $request)
    {
        $item = Item::findOrFail($request->id_barang);

        $rules = [
            'id_barang'     => 'required|exists:items,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tujuan_pinjam' => 'required|string|max:255',
            'jam_kembali'   => 'nullable|date_format:H:i',
        ];

        if ($item->harga <= 10_000_000) {
            $rules['tanggal_kembali'] = 'required|date|after:today';
        }

        $validated = $request->validate($rules, [
            'tanggal_kembali.required' => 'Tanggal kembali wajib diisi untuk barang ini.',
            'tanggal_kembali.after'    => 'Tanggal kembali harus setelah hari ini.',
        ]);

        try {
            $this->service->pinjam(
                auth()->id(),
                $validated['id_barang'],
                $validated['jumlah_pinjam'],
                $validated['tanggal_kembali'] ?? null,
                $validated['tujuan_pinjam'],
                $validated['jam_kembali'] ?? null,
            );

            return redirect()->route('borrowings.index')
                ->with('success', 'Pengajuan peminjaman berhasil dikirim, menunggu persetujuan admin.');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // =========================================================
    // UPDATE — approve / tolak / kembalikan
    // =========================================================

    #[OA\Put(
        path: "/admin/borrowings/{id}",
        summary: "Ubah status peminjaman (approve / tolak / kembalikan)",
        description: "Admin dapat menyetujui (approve), menolak (tolak), atau menandai barang sudah dikembalikan (kembali).",
        tags: ["Peminjaman"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID peminjaman",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["action"],
                properties: [
                    new OA\Property(property: "action", type: "string", enum: ["approve", "tolak", "kembali"], example: "approve"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect dengan pesan sukses"),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "Peminjaman tidak ditemukan"),
        ]
    )]
    public function update(Request $request, $id)
    {
        $borrowing = Borrowing::with('item')->findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,tolak,kembali',
        ]);

        try {
            match ($request->action) {
                'approve' => $this->service->approve($borrowing, auth()->id()),
                'tolak'   => $this->service->tolak($borrowing, auth()->id()),
                'kembali' => $this->service->kembalikan($borrowing),
            };

            $pesan = match ($request->action) {
                'approve' => 'Peminjaman berhasil disetujui.',
                'tolak'   => 'Peminjaman berhasil ditolak.',
                'kembali' => 'Barang berhasil dikembalikan.',
            };

            return back()->with('success', $pesan);

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // =========================================================
    // SHOW — detail satu peminjaman
    // =========================================================

    #[OA\Get(
        path: "/borrowings/{id}",
        summary: "Detail peminjaman",
        description: "Menampilkan detail satu peminjaman. Admin melihat semua peminjaman; user biasa hanya bisa melihat milik sendiri.",
        tags: ["Peminjaman"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID peminjaman",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman detail peminjaman"),
            new OA\Response(response: 404, description: "Peminjaman tidak ditemukan"),
        ]
    )]
    public function show($id)
    {
        $borrowing = Borrowing::with(['item.cabinet', 'user', 'admin', 'documents'])
            ->findOrFail($id);

        if (auth()->user()->role === 'admin') {
            return view('admin.borrowings.show', compact('borrowing'));
        }
        return view('user.borrowings.show', compact('borrowing'));
    }
}