<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

class Cabinet extends Model
{
    protected $table = 'cabinets';

    protected $fillable = [
        'kode_lemari',
        'nama_lemari',
        'id_ruangan',
    ];

    // 🔥 RELASI KE ROOM
    public function room()
    {
        return $this->belongsTo(Room::class, 'id_ruangan');
    }

    // 🔥 RELASI KE ITEMS (nanti dipakai)
    public function items()
    {
        return $this->hasMany(Item::class, 'id_lemari');
    }
}
