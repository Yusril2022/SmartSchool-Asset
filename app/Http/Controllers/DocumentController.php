<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Item;
use App\Models\Borrowing;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $documentService) {}

    // =========================================================
    // LIST — semua dokumen
    // =========================================================
    public function index()
    {
        $documents = Document::with(['uploadedBy', 'item', 'borrowing'])
            ->latest()
            ->paginate(15);

        return view('admin.documents.index', compact('documents'));
    }

    // =========================================================
    // FORM — upload dokumen manual
    // =========================================================
    public function create()
    {
        $items      = Item::orderBy('nama_barang')->get();
        $borrowings = Borrowing::with(['item', 'user'])
            ->whereIn('status', ['dipinjam', 'dikembalikan'])
            ->latest()
            ->get();

        return view('admin.documents.create', compact('items', 'borrowings'));
    }

    // =========================================================
    // STORE — simpan dokumen manual
    // =========================================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_dokumen'   => 'required|string|max:255',
            'jenis_dokumen'   => 'required|string|max:255',
            'no_dokumen'      => 'nullable|string|max:255',
            'tanggal_dokumen' => 'required|date',
            'pihak_terkait'   => 'nullable|string|max:255',
            'id_barang'       => 'nullable|exists:items,id',
            'id_peminjaman'   => 'nullable|exists:borrowings,id',
            'file_path'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            // max 5MB
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
            'id_barang'       => $validated['id_barang'] ?? null,
            'id_peminjaman'   => $validated['id_peminjaman'] ?? null,
            'file_path'       => $filePath,
            'keterangan'      => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil disimpan.');
    }

    // =========================================================
    // SHOW - file dokumen
    // =========================================================

    public function show(Document $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'File dokumen tidak ditemukan.');
        }

        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $url = Storage::disk('public')->url($document->file_path);

        return view('admin.documents.show', compact('document', 'url', 'extension'));
    }

    // =========================================================
    // DOWNLOAD — download file dokumen
    // =========================================================
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

    // =========================================================
    // DESTROY — hapus dokumen
    // =========================================================
    public function destroy(Document $document)
    {
        // Hapus file dari storage jika ada
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}