<?php

namespace App\Services;

use App\Models\User;
use App\Models\Document;
use App\Models\Borrowing;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Generate PDF berita acara on-the-fly dan langsung stream ke browser.
     * Tidak menyimpan file, tidak mencatat ke tabel documents.
     */
    public function streamBeritaAcara(Borrowing $borrowing)
    {
        $borrowing->loadMissing(['item.cabinet.room', 'user', 'admin']);

        $penandatangan = User::getPenandatangan();

        $pdf = Pdf::loadView('admin.documents.official-report', [
            'borrowing'     => $borrowing,
            'item'          => $borrowing->item,
            'user'          => $borrowing->user,
            'admin'         => $borrowing->admin,
            'penandatangan' => $penandatangan,
        ])->setPaper([0, 0, 609.45, 935.43], 'portrait');

        $fileName = 'berita-acara-' . $borrowing->kode_peminjaman . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Download file dokumen arsip manual.
     */
    public function download(Document $document): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            throw new \Exception('File dokumen tidak ditemukan.');
        }

        return Storage::disk('public')->download($document->file_path);
    }
}