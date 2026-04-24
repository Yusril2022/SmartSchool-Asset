<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomingItem extends Model
{
    protected $table = 'incoming_items';

    protected $fillable = [
        'id_barang',
        'id_admin',
        'jumlah_masuk',
        'tanggal_masuk',
    ];

    protected $casts = [
        'jumlah_masuk'  => 'integer',
        'tanggal_masuk' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'id_barang');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}