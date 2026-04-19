<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nomor_induk',
        'email',
        'no_hp',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peminjamans()
    {
        return $this->hasMany(TransaksiPeminjaman::class, 'user_id');
    }

    public function approvals()
    {
        return $this->hasMany(TransaksiPeminjaman::class, 'admin_id');
    }

    public function barangMasuk()
    {
        return $this->hasMany(RiwayatBarangMasuk::class, 'admin_id');
    }

    public function userIndex(Request $request)
{
    $query = MasterBarang::query();

    if ($request->search) {
        $query->where('nama_barang', 'like', '%' . $request->search . '%');
    }

    $barangs = $query->get();

    return view('user.barang.index', compact('barangs'));
}
}
