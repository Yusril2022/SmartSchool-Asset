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

    // Cek apakah user adalah admin (berguna di Blade: auth()->user()->isAdmin())
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // =========================================================
    // RELASI
    // =========================================================

    // Semua peminjaman yang diajukan user ini
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class, 'id_user');
    }

    // Semua peminjaman yang di-approve/tolak oleh user ini (sebagai admin)
    public function approvals()
    {
        return $this->hasMany(Borrowing::class, 'id_admin');
    }

    // Semua barang masuk yang diinput oleh user ini (sebagai admin)
    public function incomingItems()
    {
        return $this->hasMany(IncomingItem::class, 'id_admin');
    }

    // Semua pengambilan barang konsumsi oleh user ini
    public function itemUsages()
    {
        return $this->hasMany(ItemUsage::class, 'id_user');
    }
}