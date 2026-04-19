<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Borrowing;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'kode_barang',
        'id_lemari',
        'nama_barang',
        'kategori',
        'jenis_barang',
        'stok_awal',
        'stok_total',
        'batas_minimum',
        'harga'
    ];

    // 🔥 RELASI KE CABINET
    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class, 'id_lemari');
    }

    // 🔥 RELASI KE BORROWINGS (nanti)
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'id_barang');
    }
}