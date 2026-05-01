<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemUsage extends Model
{
    protected $table = 'item_usages';

    protected $fillable = [
        'id_barang',
        'id_user',          
        'nama_pengambil',
        'sebagai',   
        'jumlah_ambil',
        'tanggal_ambil',
    ];

    protected $casts = [
        'jumlah_ambil'  => 'integer',
        'tanggal_ambil' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_barang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Nama yang tampil di riwayat — pakai nama user kalau login,
     * pakai nama_pengambil kalau tidak login.
     */
    public function getNamaDisplayAttribute(): string
    {
        return $this->user?->name ?? $this->nama_pengambil ?? 'Tidak diketahui';
    }
}