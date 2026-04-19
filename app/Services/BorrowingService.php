<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Borrowing;

class BorrowingService
{
    public function pinjam($userId, $itemId, $jumlah)
    {
        $item = Item::findOrFail($itemId);

        if ($item->stok_total < $jumlah) {
            throw new \Exception('Stok tidak cukup');
        }

        return Borrowing::create([
            'kode_peminjaman' => 'TRX-' . time(),
            'id_user' => $userId,
            'id_barang' => $itemId,
            'jumlah_pinjam' => $jumlah,
            'status' => 'pending',
            'tanggal_peminjaman' => now(),
        ]);
    }

    public function approve($borrowing, $adminId)
    {
        $item = $borrowing->item;

        if ($item->stok_total < $borrowing->jumlah_pinjam) {
            throw new \Exception('Stok tidak cukup');
        }

        $item->decrement('stok_total', $borrowing->jumlah_pinjam);

        $borrowing->update([
            'status' => 'dipinjam',
            'id_admin' => $adminId
        ]);
    }

    public function kembalikan($borrowing)
    {
        $item = $borrowing->item;

        $item->increment('stok_total', $borrowing->jumlah_pinjam);

        $borrowing->update([
            'status' => 'dikembalikan',
            'tanggal_kembali' => now()
        ]);
    }
}