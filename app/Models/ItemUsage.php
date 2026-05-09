<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ItemUsage extends Model
{
    protected $table = 'item_usages';

    protected $fillable = [
        'session_id',
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

    // =========================================================
    // BOOT — auto generate session_id kalau tidak diisi
    // =========================================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->session_id)) {
                $model->session_id = (string) Str::uuid();
            }
        });
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_barang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // =========================================================
    // COMPUTED
    // =========================================================

    public function getNamaDisplayAttribute(): string
    {
        return $this->user?->name ?? $this->nama_pengambil ?? 'Tidak diketahui';
    }
}