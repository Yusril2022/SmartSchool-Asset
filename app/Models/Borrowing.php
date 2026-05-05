<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $table = 'borrowings';

    protected $fillable = [
        'kode_peminjaman',
        'id_user',
        'id_admin',
        'id_barang',
        'jumlah_pinjam',
        'tujuan_pinjam',
        'status',
        'tanggal_peminjaman',
        'tanggal_kembali',
    ];

    // 🔥 RELASI
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_barang');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_peminjaman');
    }
}