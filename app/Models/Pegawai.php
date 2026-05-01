<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawais';

    protected $fillable = [
        'nama',
        'nip',
        'jabatan',
        'aktif',
    ];

    // Scope — hanya pegawai aktif
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }
}