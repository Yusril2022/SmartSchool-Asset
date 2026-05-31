<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $documentService) {}

    #[OA\Get(
        path: "/documents",
        summary: "Daftar semua dokumen (admin)",
        description: "Menampilkan daftar semua dokumen arsip dengan filter judul dan rentang tanggal. Hanya untuk admin.",
        tags: ["Dokumen"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, description: "Cari berdasarkan judul dokumen", schema: new OA\Schema(type: "string", example: "Berita Acara")),
            new OA\Parameter(name: "dari", in: "query", required: false, description: "Tanggal mulai (Y-m-d)", schema: new OA\Schema(type: "string", format: "date", example: "2025-05-01")),
            new OA\Parameter(name: "sampai", in: "query", required: false, description: "Tanggal akhir (Y-m-d)", schema: new OA\Schema(type: "string", format: "date", example: "2025-05-31")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar dokumen"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index(Request $request)
    {
        $query = Document::with(['uploadedBy', 'item', 'borrowing'])->latest();

        if ($request->search) {
            $query->where('judul_dokumen', 'like', '%' . $request->search . '%');
        }
        if ($request->dari) {
            $query->whereDate('tanggal_dokumen', '>=', $request->dari);
        }
        if ($request->sampai) {
            $query->whereDate('tanggal_dokumen', '<=', $request->sampai);
        }

        $documents = $query->paginate(15)->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    #[OA\Get(
        path: "/documents/create",
        summary: "Form upload dokumen (admin)",
        description: "Menampilkan form untuk mengupload dokumen arsip baru secara manual. Hanya untuk admin.",
        tags: ["Dokumen"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman form upload dokumen"),
        ]
    )]
    public function create()
    {
        return view('admin.documents.create');
    }

    #[OA\Post(
        path: "/documents",
        summary: "Simpan dokumen baru (admin)",
        description: "Menyimpan dokumen arsip baru. File yang diizinkan: PDF, JPG, JPEG, PNG (max 5MB).",
        tags: ["Dokumen"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["judul_dokumen", "jenis_dokumen", "tanggal_dokumen"],
                    properties: [
                        new OA\Property(property: "judul_dokumen", type: "string", example: "Berita Acara Serah Terima Laptop"),
                        new OA\Property(property: "jenis_dokumen", type: "string", example: "Berita Acara", description: "Jenis/kategori dokumen"),
                        new OA\Property(property: "no_dokumen", type: "string", example: "BA/001/2025", description: "Nomor dokumen (opsional)"),
                        new OA\Property(property: "tanggal_dokumen", type: "string", format: "date", example: "2025-05-30"),
                        new OA\Property(property: "pihak_terkait", type: "string", example: "Dinas Pendidikan", description: "Pihak yang terkait dalam dokumen (opsional)"),
                        new OA\Property(property: "keterangan", type: "string", example: "Dokumen serah terima 5 unit laptop", description: "Keterangan tambahan (opsional)"),
                        new OA\Property(property: "file_path", type: "string", format: "binary", description: "File dokumen (pdf/jpg/jpeg/png, max 5MB, opsional)"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar dokumen jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_dokumen'   => 'required|string|max:255',
            'jenis_dokumen'   => 'required|string|max:255',
            'no_dokumen'      => 'nullable|string|max:255',
            'tanggal_dokumen' => 'required|date',
            'pihak_terkait'   => 'nullable|string|max:255',
            'file_path'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan'      => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file_path')) {
            $filePath = $request->file('file_path')
                ->store('documents/arsip', 'public');
        }

        Document::create([
            'uploaded_by'     => auth()->id(),
            'judul_dokumen'   => $validated['judul_dokumen'],
            'jenis_dokumen'   => $validated['jenis_dokumen'],
            'no_dokumen'      => $validated['no_dokumen'] ?? null,
            'tanggal_dokumen' => $validated['tanggal_dokumen'],
            'pihak_terkait'   => $validated['pihak_terkait'] ?? null,
            'id_barang'       => null,
            'id_peminjaman'   => null,
            'file_path'       => $filePath,
            'keterangan'      => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil disimpan.');
    }

    #[OA\Get(
        path: "/documents/{id}",
        summary: "Lihat dokumen (admin)",
        description: "Menampilkan halaman preview dokumen. Jika file tidak ditemukan di storage, akan redirect dengan pesan error.",
        tags: ["Dokumen"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID dokumen", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman preview dokumen"),
            new OA\Response(response: 302, description: "Redirect dengan error jika file tidak ditemukan"),
            new OA\Response(response: 404, description: "Dokumen tidak ditemukan"),
        ]
    )]
    public function show(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $url = Storage::disk('public')->url($document->file_path);

        return view('admin.documents.show', compact('document', 'url', 'extension'));
    }

    #[OA\Get(
        path: "/documents/{id}/download",
        summary: "Download file dokumen (admin)",
        description: "Mendownload file dokumen langsung ke perangkat pengguna.",
        tags: ["Dokumen"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID dokumen", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: "File dokumen (pdf/jpg/jpeg/png)"),
            new OA\Response(response: 302, description: "Redirect dengan error jika file tidak ditemukan"),
            new OA\Response(response: 404, description: "Dokumen tidak ditemukan"),
        ]
    )]
    public function download(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->judul_dokumen . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION)
        );
    }

    #[OA\Delete(
        path: "/documents/{id}",
        summary: "Hapus dokumen (admin)",
        description: "Menghapus dokumen beserta file-nya dari storage. Hanya untuk admin.",
        tags: ["Dokumen"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "ID dokumen", schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 302, description: "Redirect dengan pesan sukses"),
            new OA\Response(response: 404, description: "Dokumen tidak ditemukan"),
        ]
    )]
    public function destroy(Document $document)
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}