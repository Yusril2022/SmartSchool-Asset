<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {

    // Notifikasi
    Route::get('/borrowings/terlambat', [ApiController::class, 'borrowingsTerlambat']);
    Route::get('/items/stok-kritis',    [ApiController::class, 'stokKritis']);

    // Laporan bulanan
    Route::get('/laporan/peminjaman',   [ApiController::class, 'laporanPeminjaman']);
    Route::get('/laporan/pengambilan',  [ApiController::class, 'laporanPengambilan']);
    Route::get('/laporan/barang-masuk', [ApiController::class, 'laporanBarangMasuk']);

    // Rekap harian
    Route::get('/rekap/hari-ini',       [ApiController::class, 'rekapHariIni']);
    Route::post('/borrowings/tandai-notifikasi', [ApiController::class, 'tandaiNotifikasi']);
});