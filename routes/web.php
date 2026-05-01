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
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PublicItemUsageController;

Route::get('/', function () {
    return view('welcome');
});

// =========================================================
// 🌐 PUBLIC — Tidak perlu login sama sekali
// =========================================================

Route::get('/scan/{kode}', [ItemController::class, 'scan'])
    ->name('items.scan');

// Form pengambilan konsumsi tanpa login
Route::get('/ambil', [ItemUsageController::class, 'formPublic'])
    ->name('ambil.form');
Route::post('/ambil', [ItemUsageController::class, 'storePublic'])
    ->name('ambil.store');
Route::get('/ambil/sukses', [ItemUsageController::class, 'sukses'])
    ->name('ambil.sukses');

// =========================================================
// 🔵 SEMUA USER YANG SUDAH LOGIN
// =========================================================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Halaman kamera scan QR — semua role bisa akses (admin, siswa, guru)
    Route::get('/scan-barang', function () {
        return view('user.scan');
    })->name('scan.barang');
});

// =========================================================
// 🔴 ADMIN ONLY
// =========================================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('users', UserController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('cabinets', CabinetController::class);
    Route::resource('items', ItemController::class);

    Route::resource('incoming-items', IncomingItemController::class)
        ->only(['index', 'create', 'store']);

    Route::resource('admin/borrowings', BorrowingController::class)
        ->only(['index', 'show', 'update'])
        ->names([
            'index'  => 'admin.borrowings.index',
            'show'   => 'admin.borrowings.show',
            'update' => 'admin.borrowings.update',
        ]);

    Route::get('admin/item-usages', [ItemUsageController::class, 'adminIndex'])
    ->name('admin.item-usages.index');

    Route::resource('documents', DocumentController::class)
        ->only(['index', 'create', 'store',  'show', 'destroy']);

    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
});

// =========================================================
// 🟢 USER BIASA (siswa DAN guru)
// =========================================================
Route::middleware(['auth', 'role:siswa,guru'])->group(function () {

    Route::get('/catalog', [ItemController::class, 'userIndex'])
        ->name('items.user');
    Route::get('/catalog/{id}', [ItemController::class, 'showUser'])
        ->name('items.user.show');

    // Peminjaman barang ASET
    Route::get('/borrowings/create/{id}', [BorrowingController::class, 'create'])
        ->name('borrowings.create');
    Route::resource('borrowings', BorrowingController::class)
        ->only(['index', 'store', 'show']);

    // Pengambilan barang KONSUMSI (login)
    Route::get('/item-usages/{id}', [ItemUsageController::class, 'create'])
        ->name('item-usages.create');
    Route::post('/item-usages', [ItemUsageController::class, 'store'])
        ->name('item-usages.store');
    Route::get('/item-usages', [ItemUsageController::class, 'index'])
        ->name('item-usages.index');
});

require __DIR__ . '/auth.php';