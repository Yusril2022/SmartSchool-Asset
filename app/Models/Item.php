<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items';

    protected $fillable = [
        'kode_barang',
        'id_lemari',
        'nama_barang',
        'kategori',
        'jenis_barang',
        'merk',          
        'hasil_perolehan',
        'stok_awal',
        'stok_total',
        'batas_minimum',
        'harga',
        'foto',
    ];

    protected $casts = [
        'harga'         => 'integer',
        'stok_total'    => 'integer',
        'stok_awal'     => 'integer',
        'batas_minimum' => 'integer',
    ];

    // =========================================================
    // COMPUTED: Apakah stok di bawah batas minimum?
    // =========================================================
    public function getStokKritisAttribute(): bool
    {
        return $this->stok_total <= $this->batas_minimum;
    }

    // =========================================================
    // COMPUTED: Apakah barang ini mahal (> 10 juta)?
    // =========================================================
    public function getIsBarangMahalAttribute(): bool
    {
        return $this->harga > 10_000_000;
    }

    // =========================================================
    // RELASI
    // =========================================================

    // Barang berada di lemari mana
    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class, 'id_lemari');
    }

    // Riwayat peminjaman barang ini
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'id_barang');
    }

    // Riwayat barang masuk
    public function incomingItems()
    {
        return $this->hasMany(IncomingItem::class, 'id_barang');
    }

    // Riwayat pengambilan barang konsumsi
    public function itemUsages()
    {
        return $this->hasMany(ItemUsage::class, 'id_barang');
    }

    // Dokumen berita acara yang terkait
    public function documents()
    {
        return $this->hasMany(Document::class, 'id_barang');
    }

    // =========================================================
    // SCOPES: untuk filter di query
    // =========================================================

    // Hanya barang aset
    public function scopeAset($query)
    {
        return $query->where('jenis_barang', 'aset');
    }

    // Hanya barang konsumsi
    public function scopeKonsumsi($query)
    {
        return $query->where('jenis_barang', 'konsumsi');
    }

    // Hanya barang yang stoknya kritis
    public function scopeStokKritis($query)
    {
        return $query->whereColumn('stok_total', '<=', 'batas_minimum');
    }
}