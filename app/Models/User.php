<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nomor_induk',
        'email',
        'no_hp',
        'password',
        'role',
        'jabatan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // =========================================================
    // HELPERS
    // =========================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Cari penandatangan berita acara:
    // Utamakan Kepala Sekolah, fallback ke Wakasek Sarpras
    public static function getPenandatangan(): ?self
    {
        return self::where('jabatan', 'Kepala Sekolah')->first()
            ?? self::where('jabatan', 'Wakasek Sarana dan Prasarana')->first();
    }

    // =========================================================
    // RELASI
    // =========================================================

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'id_user');
    }

    public function approvals()
    {
        return $this->hasMany(Borrowing::class, 'id_admin');
    }

    public function incomingItems()
    {
        return $this->hasMany(IncomingItem::class, 'id_admin');
    }

    public function itemUsages()
    {
        return $this->hasMany(ItemUsage::class, 'id_user');
    }
}