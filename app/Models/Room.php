<?php

namespace App\Models;

use App\Models\Cabinet;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
    ];

    // relasi nanti dipakai di cabinets
    public function cabinets()
    {
        return $this->hasMany(Cabinet::class, 'id_ruangan');
    }
}