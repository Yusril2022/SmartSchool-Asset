<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LemariController;
use App\Http\Controllers\LokasiRuanganController;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiPeminjamanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// 🔵 SEMUA USER LOGIN
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/scan/{kode}', [MasterBarangController::class, 'scan'])
        ->name('barang.scan');
});


// 🔴 ADMIN ONLY
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('users', UserController::class);

    Route::resource('lokasi-ruangan', LokasiRuanganController::class);
    Route::resource('lemari', LemariController::class);
    Route::resource('master-barang', MasterBarangController::class);

    // ✅ ADMIN PUNYA PREFIX
    Route::resource('admin/peminjaman', TransaksiPeminjamanController::class)
        ->only(['index', 'update'])
        ->names([
            'index' => 'admin.peminjaman.index',
            'update' => 'admin.peminjaman.update',
        ]);
});


// 🟢 USER ONLY
Route::middleware(['auth', 'role:siswa'])->group(function () {

    Route::get('/barang-user', [MasterBarangController::class, 'userIndex'])
        ->name('barang.user');

    Route::get('/barang-user/{id}', [MasterBarangController::class, 'showUser'])
        ->name('barang.user.show');

    Route::get('/peminjaman/create/{id}', [TransaksiPeminjamanController::class, 'create'])
        ->name('peminjaman.create');

    Route::resource('peminjaman', TransaksiPeminjamanController::class)
        ->except(['create', 'show', 'update']);

    Route::get('/scan-barang', function () {
        return view('user.scan');
    })->name('scan.barang');
});


require __DIR__ . '/auth.php';
