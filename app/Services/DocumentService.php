<?php

namespace App\Services;

use App\Models\User;
use App\Models\Document;
use App\Models\Borrowing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function generateBeritaAcara(Borrowing $borrowing): Document
    {
        $borrowing->loadMissing(['item.cabinet.room', 'user', 'admin']);

        // Cari penandatangan: Kepala Sekolah → fallback Wakasek Sarpras → null
        $penandatangan = User::getPenandatangan();

        $fileName   = 'berita-acara-' . $borrowing->kode_peminjaman . '.pdf';
        $folderPath = 'admin.documents.official-report';
        $filePath   = $folderPath . '/' . $fileName;

        $pdf = Pdf::loadView('admin.documents.official-report', [
            'borrowing'     => $borrowing,
            'item'          => $borrowing->item,
            'user'          => $borrowing->user,
            'admin'         => $borrowing->admin,
            'penandatangan' => $penandatangan,
        ])->setPaper([0, 0, 609.45, 935.43], 'portrait'); // F4 dalam points

        Storage::disk('public')->put($filePath, $pdf->output());

        return Document::create([
            'uploaded_by'     => $borrowing->id_admin,
            'id_barang'       => $borrowing->id_barang,
            'id_peminjaman'   => $borrowing->id,
            'judul_dokumen'   => 'Berita Acara Peminjaman ' . $borrowing->item->nama_barang,
            'jenis_dokumen'   => 'Berita Acara Peminjaman',
            'no_dokumen'      => 'BA-' . $borrowing->kode_peminjaman,
            'tanggal_dokumen' => now()->toDateString(),
            'pihak_terkait'   => $borrowing->user->name,
            'file_path'       => $filePath,
            'keterangan'      => 'Auto-generated saat approve peminjaman ' . $borrowing->kode_peminjaman,
        ]);
    }

    public function download(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            throw new \Exception('File dokumen tidak ditemukan.');
        }

        return Storage::disk('public')->download($document->file_path);
    }
}