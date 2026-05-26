<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemConditionLog extends Model
{
    protected $table = 'item_condition_logs';

    protected $fillable = [
        'id_barang',
        'id_admin',
        'kondisi_lama',
        'kondisi_baru',
        'catatan',
    ];

    // Barang yang terkait
    public function item()
    {
        return $this->belongsTo(Item::class, 'id_barang');
    }

    // Admin yang mengubah kondisi
    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin');
    }
}