<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Borrowing;
use App\Services\DocumentService;

class BorrowingService
{
    public function __construct(protected DocumentService $documentService) {}

    // =========================================================
    // USER: Ajukan peminjaman
    // =========================================================
    public function pinjam(int $userId, int $itemId, int $jumlah, ?string $tanggalKembali, ?string $tujuanPinjam = null): Borrowing
    {
        $item = Item::findOrFail($itemId);

        // Guard: hanya barang aset yang bisa dipinjam
        if ($item->jenis_barang !== 'aset') {
            throw new \Exception('Barang konsumsi tidak bisa dipinjam, gunakan fitur pengambilan.');
        }

        if ($item->stok_total < $jumlah) {
            throw new \Exception('Stok tidak mencukupi.');
        }

        // Validasi tanggal kembali berdasarkan harga
        if ($item->harga <= 10_000_000 && empty($tanggalKembali)) {
            throw new \Exception('Tanggal kembali wajib diisi untuk barang dengan harga di bawah 10 juta.');
        }

        return Borrowing::create([
            'kode_peminjaman'   => 'TRX-' . strtoupper(uniqid()),
            'id_user'           => $userId,
            'id_barang'         => $itemId,
            'jumlah_pinjam'     => $jumlah,
            'tujuan_pinjam'     => $tujuanPinjam,
            'status'            => 'pending',
            'tanggal_peminjaman' => now(),
            'tanggal_kembali'   => $item->harga > 10_000_000 ? null : $tanggalKembali,
        ]);
    }

    // =========================================================
    // ADMIN: Setujui peminjaman
    // =========================================================
    public function approve(Borrowing $borrowing, int $adminId, ?string $tanggalKembali = null): void
    {
        if ($borrowing->status !== 'pending') {
            throw new \Exception('Peminjaman ini sudah diproses sebelumnya.');
        }

        $item = $borrowing->item;

        if ($item->stok_total < $borrowing->jumlah_pinjam) {
            throw new \Exception('Stok tidak mencukupi saat approve.');
        }

        // Barang > 10 juta: tanggal kembali wajib diisi admin saat approve
        if ($item->harga > 10_000_000 && empty($tanggalKembali)) {
            throw new \Exception('Tanggal kembali wajib ditentukan oleh admin untuk barang di atas 10 juta.');
        }

        $item->decrement('stok_total', $borrowing->jumlah_pinjam);

        $borrowing->update([
            'status'          => 'dipinjam',
            'id_admin'        => $adminId,
            'tanggal_kembali' => $item->harga > 10_000_000 ? $tanggalKembali : $borrowing->tanggal_kembali,
        ]);

        // Auto-generate dokumen jika harga barang > 10 juta
        if ($item->harga > 10_000_000) {
            $this->documentService->generateBeritaAcara($borrowing->fresh(['item', 'user', 'admin']));
        }
    }

    // =========================================================
    // ADMIN/USER: Kembalikan barang
    // =========================================================
    public function kembalikan(Borrowing $borrowing): void
    {
        // Guard: hanya bisa kembalikan jika sedang dipinjam
        if ($borrowing->status !== 'dipinjam') {
            throw new \Exception('Barang ini tidak sedang dalam status dipinjam.');
        }

        $borrowing->item->increment('stok_total', $borrowing->jumlah_pinjam);

        $borrowing->update([
            'status'          => 'dikembalikan',
            'tanggal_kembali' => now(),
        ]);
    }

    // =========================================================
    // ADMIN: Tolak peminjaman
    // =========================================================
    public function tolak(Borrowing $borrowing, int $adminId): void
    {
        if ($borrowing->status !== 'pending') {
            throw new \Exception('Hanya peminjaman dengan status pending yang bisa ditolak.');
        }

        $borrowing->update([
            'status'   => 'ditolak',
            'id_admin' => $adminId,
        ]);
    }
}