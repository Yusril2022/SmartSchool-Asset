<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomingItemController;
use App\Http\Controllers\ItemUsageController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// =========================================================
// 🔵 SEMUA USER YANG SUDAH LOGIN
// =========================================================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // QR scan — semua user bisa scan
    Route::get('/scan/{kode}', [ItemController::class, 'scan'])
        ->name('items.scan');
});

// =========================================================
// 🔴 ADMIN ONLY
// =========================================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Master data
    Route::resource('users', UserController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('cabinets', CabinetController::class);
    Route::resource('items', ItemController::class);

    // Barang masuk (hanya admin yang bisa input)
    Route::resource('incoming-items', IncomingItemController::class)
        ->only(['index', 'create', 'store']);

    // Peminjaman — admin lihat semua & bisa approve/tolak/kembalikan
    Route::resource('admin/borrowings', BorrowingController::class)
        ->only(['index', 'show', 'update'])
        ->names([
            'index'  => 'admin.borrowings.index',
            'show'   => 'admin.borrowings.show',
            'update' => 'admin.borrowings.update',
        ]);
});

// =========================================================
// 🟢 USER BIASA (siswa DAN guru bisa akses)
// =========================================================
Route::middleware(['auth', 'role:siswa,guru'])->group(function () {

    // Lihat daftar barang
    Route::get('/catalog', [ItemController::class, 'userIndex'])
        ->name('items.user');
    Route::get('/catalog/{id}', [ItemController::class, 'showUser'])
        ->name('items.user.show');

    // Peminjaman barang ASET
    Route::get('/borrowings/create/{id}', [BorrowingController::class, 'create'])
        ->name('borrowings.create');
    Route::resource('borrowings', BorrowingController::class)
        ->only(['index', 'store', 'show']);

    // Pengambilan barang KONSUMSI
    Route::get('/item-usages/{id}', [ItemUsageController::class, 'create'])
        ->name('item-usages.create');
    Route::post('/item-usages', [ItemUsageController::class, 'store'])
        ->name('item-usages.store');
    Route::get('/item-usages', [ItemUsageController::class, 'index'])
        ->name('item-usages.index');

    // Halaman scan QR
    Route::get('/scan-barang', function () {
        return view('user.scan');
    })->name('scan.barang');
});

require __DIR__ . '/auth.php';