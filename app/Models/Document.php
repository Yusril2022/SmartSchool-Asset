<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
    'uploaded_by',
    'id_barang',
    'id_peminjaman',
    'judul_dokumen',
    'jenis_dokumen',
    'no_dokumen',
    'tanggal_dokumen',
    'pihak_terkait',
    'file_path',
    'keterangan',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
    ];

    // Barang yang terkait dokumen ini
    public function item()
    {
        return $this->belongsTo(Item::class, 'id_barang');
    }

    // Peminjaman yang terkait dokumen ini
    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class, 'id_peminjaman');
    }

    // Admin yang generate dokumen ini
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}