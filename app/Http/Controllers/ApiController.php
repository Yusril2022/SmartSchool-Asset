<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\IncomingItem;
use App\Models\ItemUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class ApiController extends Controller
{
    // =========================================================
    // 1. Peminjaman yang terlambat
    // =========================================================

    #[OA\Get(
        path: "/api/borrowings/terlambat",
        summary: "Daftar peminjaman yang terlambat dikembalikan",
        description: "Mengembalikan semua peminjaman berstatus 'dipinjam' yang melewati tanggal/jam kembali. Digunakan oleh n8n untuk notifikasi otomatis.",
        tags: ["API - Laporan"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Daftar peminjaman terlambat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                        new OA\Property(property: "total", type: "integer", example: 3),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "kode_peminjaman", type: "string", example: "BRW-20240101-001"),
                                    new OA\Property(property: "nama_peminjam", type: "string", example: "Budi Santoso"),
                                    new OA\Property(property: "email_peminjam", type: "string", example: "budi@sekolah.sch.id"),
                                    new OA\Property(property: "no_hp", type: "string", example: "081234567890"),
                                    new OA\Property(property: "nama_barang", type: "string", example: "Laptop Dell"),
                                    new OA\Property(property: "jumlah_pinjam", type: "integer", example: 1),
                                    new OA\Property(property: "tanggal_kembali", type: "string", format: "date", example: "2024-05-01"),
                                    new OA\Property(property: "jam_kembali", type: "string", example: "14:00"),
                                    new OA\Property(property: "terlambat", type: "string", example: "3 hari yang lalu"),
                                ]
                            )
                        ),
                    ]
                )
            )
        ]
    )]
    public function borrowingsTerlambat()
    {
        $sekarang = now();

        $data = Borrowing::with(['item', 'user'])
            ->where('status', 'dipinjam')
            ->whereNotNull('tanggal_kembali')
            ->get()
            ->filter(function ($b) use ($sekarang) {
                if ($b->notifikasi_terkirim_at) {
                    $sudahNotif = Carbon::parse($b->notifikasi_terkirim_at);
                    if ($sudahNotif->diffInHours($sekarang) < 24) {
                        return false;
                    }
                }
                if ($b->jam_kembali) {
                    $tanggalFormat = Carbon::parse($b->tanggal_kembali)->format('Y-m-d');
                    $deadlineJam = Carbon::parse($tanggalFormat . ' ' . $b->jam_kembali);
                    return $sekarang->gt($deadlineJam);
                }
                return $sekarang->startOfDay()->gt(
                    Carbon::parse($b->tanggal_kembali)->startOfDay()
                );
            })
            ->map(fn($b) => [
                'kode_peminjaman'  => $b->kode_peminjaman,
                'nama_peminjam'    => $b->user->name ?? '-',
                'email_peminjam'   => $b->user->email ?? '-',
                'no_hp'            => $b->user->no_hp ?? '-',
                'nama_barang'      => $b->item->nama_barang ?? '-',
                'jumlah_pinjam'    => $b->jumlah_pinjam,
                'tanggal_kembali'  => $b->tanggal_kembali,
                'jam_kembali'      => $b->jam_kembali ?? 'Per hari',
                'terlambat'        => $b->jam_kembali
                    ? Carbon::parse(Carbon::parse($b->tanggal_kembali)->format('Y-m-d') . ' ' . $b->jam_kembali)->diffForHumans()
                    : Carbon::parse($b->tanggal_kembali)->diffInDays(now()) . ' hari',
            ])
            ->values();

        return response()->json([
            'status' => 'ok',
            'total'  => $data->count(),
            'data'   => $data,
        ]);
    }

    // =========================================================
    // 2. Stok kritis
    // =========================================================

    #[OA\Get(
        path: "/api/stok-kritis",
        summary: "Daftar barang dengan stok di bawah batas minimum",
        description: "Mengembalikan semua barang yang stok totalnya kurang dari atau sama dengan batas minimum.",
        tags: ["API - Laporan"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Daftar barang stok kritis",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                        new OA\Property(property: "total", type: "integer", example: 5),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "kode_barang", type: "string", example: "BRG-001"),
                                    new OA\Property(property: "nama_barang", type: "string", example: "Spidol Whiteboard"),
                                    new OA\Property(property: "jenis_barang", type: "string", enum: ["aset", "konsumsi"], example: "konsumsi"),
                                    new OA\Property(property: "stok_total", type: "integer", example: 2),
                                    new OA\Property(property: "batas_minimum", type: "integer", example: 10),
                                    new OA\Property(property: "selisih", type: "integer", example: 8),
                                    new OA\Property(property: "lokasi", type: "string", example: "Ruang Guru"),
                                ]
                            )
                        ),
                    ]
                )
            )
        ]
    )]
    public function stokKritis()
    {
        $data = Item::stokKritis()
            ->get()
            ->map(fn($i) => [
                'kode_barang'    => $i->kode_barang,
                'nama_barang'    => $i->nama_barang,
                'jenis_barang'   => $i->jenis_barang,
                'stok_total'     => $i->stok_total,
                'batas_minimum'  => $i->batas_minimum,
                'selisih'        => $i->batas_minimum - $i->stok_total,
                'lokasi'         => $i->cabinet->room->nama_ruangan
                                    ?? $i->room->nama_ruangan
                                    ?? '-',
            ]);

        return response()->json([
            'status' => 'ok',
            'total'  => $data->count(),
            'data'   => $data,
        ]);
    }

    // =========================================================
    // 3. Laporan peminjaman bulanan
    // =========================================================

    #[OA\Get(
        path: "/api/laporan/peminjaman",
        summary: "Laporan peminjaman per bulan",
        description: "Mengembalikan daftar semua peminjaman pada bulan dan tahun yang ditentukan.",
        tags: ["API - Laporan"],
        parameters: [
            new OA\Parameter(
                name: "bulan",
                in: "query",
                description: "Bulan (1-12), default bulan ini",
                required: false,
                schema: new OA\Schema(type: "integer", example: 5)
            ),
            new OA\Parameter(
                name: "tahun",
                in: "query",
                description: "Tahun, default tahun ini",
                required: false,
                schema: new OA\Schema(type: "integer", example: 2025)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan peminjaman bulanan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                        new OA\Property(property: "bulan", type: "integer", example: 5),
                        new OA\Property(property: "tahun", type: "integer", example: 2025),
                        new OA\Property(property: "total", type: "integer", example: 12),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "kode_peminjaman", type: "string", example: "BRW-20240501-001"),
                                    new OA\Property(property: "nama_peminjam", type: "string", example: "Siti Aminah"),
                                    new OA\Property(property: "nama_barang", type: "string", example: "Proyektor Epson"),
                                    new OA\Property(property: "jumlah_pinjam", type: "integer", example: 1),
                                    new OA\Property(property: "tanggal_pinjam", type: "string", format: "date", example: "2025-05-03"),
                                    new OA\Property(property: "tanggal_kembali", type: "string", format: "date", example: "2025-05-05"),
                                    new OA\Property(property: "status", type: "string", enum: ["pending", "dipinjam", "dikembalikan", "ditolak"], example: "dikembalikan"),
                                ]
                            )
                        ),
                    ]
                )
            )
        ]
    )]
    public function laporanPeminjaman(Request $request)
    {
        $bulan  = $request->get('bulan', now()->month);
        $tahun  = $request->get('tahun', now()->year);

        $data = Borrowing::with(['item', 'user'])
            ->whereMonth('tanggal_peminjaman', $bulan)
            ->whereYear('tanggal_peminjaman', $tahun)
            ->get()
            ->map(fn($b) => [
                'kode_peminjaman'  => $b->kode_peminjaman,
                'nama_peminjam'    => $b->user->name ?? '-',
                'nama_barang'      => $b->item->nama_barang ?? '-',
                'jumlah_pinjam'    => $b->jumlah_pinjam,
                'tanggal_pinjam'   => $b->tanggal_peminjaman,
                'tanggal_kembali'  => $b->tanggal_kembali ?? '-',
                'status'           => $b->status,
            ]);

        return response()->json([
            'status' => 'ok',
            'bulan'  => $bulan,
            'tahun'  => $tahun,
            'total'  => $data->count(),
            'data'   => $data,
        ]);
    }

    // =========================================================
    // 4. Laporan pengambilan bulanan
    // =========================================================

    #[OA\Get(
        path: "/api/laporan/pengambilan",
        summary: "Laporan pengambilan konsumsi per bulan",
        description: "Mengembalikan daftar pengambilan barang konsumsi pada bulan dan tahun yang ditentukan.",
        tags: ["API - Laporan"],
        parameters: [
            new OA\Parameter(
                name: "bulan",
                in: "query",
                description: "Bulan (1-12), default bulan ini",
                required: false,
                schema: new OA\Schema(type: "integer", example: 5)
            ),
            new OA\Parameter(
                name: "tahun",
                in: "query",
                description: "Tahun, default tahun ini",
                required: false,
                schema: new OA\Schema(type: "integer", example: 2025)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan pengambilan bulanan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                        new OA\Property(property: "bulan", type: "integer", example: 5),
                        new OA\Property(property: "tahun", type: "integer", example: 2025),
                        new OA\Property(property: "total", type: "integer", example: 30),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "nama_pengambil", type: "string", example: "Ahmad Fauzi"),
                                    new OA\Property(property: "sebagai", type: "string", enum: ["murid", "pegawai"], example: "murid"),
                                    new OA\Property(property: "nama_barang", type: "string", example: "Kertas A4"),
                                    new OA\Property(property: "jumlah_ambil", type: "integer", example: 2),
                                    new OA\Property(property: "tanggal_ambil", type: "string", format: "date", example: "2025-05-10"),
                                ]
                            )
                        ),
                    ]
                )
            )
        ]
    )]
    public function laporanPengambilan(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $data = ItemUsage::with('item')
            ->whereMonth('tanggal_ambil', $bulan)
            ->whereYear('tanggal_ambil', $tahun)
            ->get()
            ->map(fn($u) => [
                'nama_pengambil' => $u->nama_pengambil ?? '-',
                'sebagai'        => $u->sebagai ?? '-',
                'nama_barang'    => $u->item->nama_barang ?? '-',
                'jumlah_ambil'   => $u->jumlah_ambil,
                'tanggal_ambil'  => $u->tanggal_ambil,
            ]);

        return response()->json([
            'status' => 'ok',
            'bulan'  => $bulan,
            'tahun'  => $tahun,
            'total'  => $data->count(),
            'data'   => $data,
        ]);
    }

    // =========================================================
    // 5. Laporan barang masuk bulanan
    // =========================================================

    #[OA\Get(
        path: "/api/laporan/barang-masuk",
        summary: "Laporan barang masuk per bulan",
        description: "Mengembalikan daftar pencatatan barang masuk pada bulan dan tahun yang ditentukan.",
        tags: ["API - Laporan"],
        parameters: [
            new OA\Parameter(
                name: "bulan",
                in: "query",
                description: "Bulan (1-12), default bulan ini",
                required: false,
                schema: new OA\Schema(type: "integer", example: 5)
            ),
            new OA\Parameter(
                name: "tahun",
                in: "query",
                description: "Tahun, default tahun ini",
                required: false,
                schema: new OA\Schema(type: "integer", example: 2025)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Laporan barang masuk bulanan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                        new OA\Property(property: "bulan", type: "integer", example: 5),
                        new OA\Property(property: "tahun", type: "integer", example: 2025),
                        new OA\Property(property: "total", type: "integer", example: 8),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "nama_barang", type: "string", example: "Spidol Whiteboard"),
                                    new OA\Property(property: "jumlah_masuk", type: "integer", example: 50),
                                    new OA\Property(property: "tanggal_masuk", type: "string", format: "date", example: "2025-05-02"),
                                    new OA\Property(property: "dicatat_oleh", type: "string", example: "Admin Sekolah"),
                                ]
                            )
                        ),
                    ]
                )
            )
        ]
    )]
    public function laporanBarangMasuk(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $data = IncomingItem::with(['item', 'admin'])
            ->whereMonth('tanggal_masuk', $bulan)
            ->whereYear('tanggal_masuk', $tahun)
            ->get()
            ->map(fn($i) => [
                'nama_barang'   => $i->item->nama_barang ?? '-',
                'jumlah_masuk'  => $i->jumlah_masuk,
                'tanggal_masuk' => $i->tanggal_masuk,
                'dicatat_oleh'  => $i->admin->name ?? '-',
            ]);

        return response()->json([
            'status' => 'ok',
            'bulan'  => $bulan,
            'tahun'  => $tahun,
            'total'  => $data->count(),
            'data'   => $data,
        ]);
    }

    // =========================================================
    // 6. Rekap hari ini
    // =========================================================

    #[OA\Get(
        path: "/api/rekap-hari-ini",
        summary: "Rekap aktivitas hari ini",
        description: "Mengembalikan ringkasan jumlah aktivitas hari ini: barang masuk, pengambilan, peminjaman baru, pending, dan stok kritis.",
        tags: ["API - Laporan"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Rekap hari ini",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                        new OA\Property(property: "tanggal", type: "string", format: "date", example: "2025-05-30"),
                        new OA\Property(property: "barang_masuk", type: "integer", example: 2),
                        new OA\Property(property: "pengambilan", type: "integer", example: 15),
                        new OA\Property(property: "peminjaman_baru", type: "integer", example: 3),
                        new OA\Property(property: "pending", type: "integer", example: 1),
                        new OA\Property(property: "stok_kritis", type: "integer", example: 4),
                    ]
                )
            )
        ]
    )]
    public function rekapHariIni()
    {
        $today = now()->toDateString();

        return response()->json([
            'status'          => 'ok',
            'tanggal'         => $today,
            'barang_masuk'    => IncomingItem::whereDate('tanggal_masuk', $today)->count(),
            'pengambilan'     => ItemUsage::whereDate('tanggal_ambil', $today)->count(),
            'peminjaman_baru' => Borrowing::whereDate('tanggal_peminjaman', $today)->count(),
            'pending'         => Borrowing::where('status', 'pending')->count(),
            'stok_kritis'     => Item::stokKritis()->count(),
        ]);
    }

    // =========================================================
    // 7. Tandai notifikasi sudah terkirim
    // =========================================================

    #[OA\Post(
        path: "/api/borrowings/tandai-notifikasi",
        summary: "Tandai notifikasi peminjaman sudah terkirim",
        description: "Digunakan oleh n8n setelah berhasil mengirim notifikasi, agar peminjaman tidak dinotifikasi ulang dalam 24 jam.",
        tags: ["API - Laporan"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kode_peminjaman"],
                properties: [
                    new OA\Property(property: "kode_peminjaman", type: "string", example: "BRW-20240101-001"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil ditandai",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "ok"),
                    ]
                )
            )
        ]
    )]
    public function tandaiNotifikasi(Request $request)
    {
        $kode = $request->kode_peminjaman;

        Borrowing::where('kode_peminjaman', $kode)
            ->update(['notifikasi_terkirim_at' => now()]);

        return response()->json(['status' => 'ok']);
    }
}