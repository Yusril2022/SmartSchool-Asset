<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use App\Services\BorrowingService;
use App\Services\DocumentService;

class BorrowingController extends Controller
{
    public function __construct(
        protected BorrowingService $service,
        protected DocumentService $documentService,
    ) {}

    // =========================================================
    // DOWNLOAD BERITA ACARA — generate on-the-fly, tanpa simpan file
    // =========================================================
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
    // LIST — beda tampilan untuk admin vs user
    // =========================================================
    public function index()
{
    if (auth()->user()->role === 'admin') {
        $data = Borrowing::with(['item', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.borrowings.index', compact('data'));
    }

    $data = Borrowing::with('item')
        ->where('id_user', auth()->id())
        ->latest()
        ->paginate(10);

    return view('user.borrowings.index', compact('data'));
}

    // =========================================================
    // FORM — hanya tampil untuk barang aset
    // =========================================================
    public function create($id)
    {
        $item = Item::findOrFail($id);

        // Barang konsumsi tidak bisa dipinjam lewat sini
        if ($item->jenis_barang !== 'aset') {
            return redirect()->back()->with('error', 'Barang konsumsi tidak bisa dipinjam.');
        }

        return view('user.borrowings.create', compact('item'));
    }

    // =========================================================
    // STORE — simpan pengajuan peminjaman
    // =========================================================
    public function store(Request $request)
    {
        $item = Item::findOrFail($request->id_barang);

        // Aturan validasi dinamis: tanggal_kembali wajib jika harga <= 10 juta
        $rules = [
            'id_barang'     => 'required|exists:items,id',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tujuan_pinjam' => 'required|string|max:255',
            'jam_kembali' => 'nullable|date_format:H:i',
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
    public function show($id)
    {
        $borrowing = Borrowing::with(['item.cabinet', 'user', 'admin', 'documents'])
            ->findOrFail($id);

        // User biasa hanya bisa lihat punya sendiri
        if (auth()->user()->role === 'admin') {
            return view('admin.borrowings.show', compact('borrowing'));
        }
        return view('user.borrowings.show', compact('borrowing'));

    }
}