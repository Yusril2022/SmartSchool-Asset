<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "SmartSchool Asset API",
    version: "1.0.0",
    description: "Dokumentasi API untuk sistem manajemen aset SmartSchool. Mencakup peminjaman barang, pengambilan konsumsi, barang masuk, dan laporan."
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Development Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Masukkan token autentikasi Laravel Sanctum/Session"
)]
#[OA\Tag(name: "API - Laporan", description: "Endpoint laporan & monitoring untuk integrasi eksternal (n8n, dsb)")]
#[OA\Tag(name: "Peminjaman", description: "Manajemen peminjaman barang aset")]
#[OA\Tag(name: "Barang Masuk", description: "Pencatatan barang masuk ke gudang")]
#[OA\Tag(name: "Pengambilan Konsumsi", description: "Pengambilan barang konsumsi oleh siswa/guru")]
#[OA\Tag(name: "Barang", description: "Manajemen data barang")]
#[OA\Tag(name: "User", description: "Manajemen akun pengguna (admin)")]
#[OA\Tag(name: "Ruangan", description: "Manajemen data ruangan (admin)")]
#[OA\Tag(name: "Lemari", description: "Manajemen data lemari penyimpanan (admin)")]
#[OA\Tag(name: "Dokumen", description: "Manajemen arsip dokumen (admin)")]
#[OA\Tag(name: "Posisi Aset", description: "Laporan posisi dan lokasi barang aset (admin)")]
class SwaggerInfo extends Controller {}