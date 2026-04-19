<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use App\Services\BorrowingService;

class TransaksiPeminjamanController extends Controller
{
    protected $service;

    public function __construct(BorrowingService $service)
    {
        $this->service = $service;
    }

    // LIST
    public function index()
    {
        if (auth()->user()->role === 'admin') {

            $data = Borrowing::with(['item', 'user'])
                ->latest()
                ->get();

            return view('admin.peminjaman.index', compact('data'));
        }

        $data = Borrowing::with('item')
            ->where('id_user', auth()->id())
            ->latest()
            ->get();

        return view('user.peminjaman.index', compact('data'));
    }

    // FORM
    public function create($id)
    {
        $item = Item::findOrFail($id);

        return view('user.peminjaman.create', compact('item'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:items,id',
            'jumlah_pinjam' => 'required|integer|min:1',
        ]);

        try {
            $this->service->pinjam(
                auth()->id(),
                $request->id_barang,
                $request->jumlah_pinjam
            );

            return redirect()->route('peminjaman.index')
                ->with('success', 'Pengajuan berhasil');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // UPDATE (APPROVE / KEMBALI)
    public function update(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);

        try {
            if ($request->action == 'approve') {
                $this->service->approve($borrowing, auth()->id());
            }

            if ($request->action == 'kembali') {
                $this->service->kembalikan($borrowing);
            }

            return back()->with('success', 'Status diperbarui');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}