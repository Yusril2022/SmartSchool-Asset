<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\IncomingItem;
use App\Models\ItemUsage;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ApiController extends Controller
{
    // =========================================================
    // 1. Peminjaman yang terlambat
    // =========================================================
// SESUDAH — cek per jam dan per hari
public function borrowingsTerlambat()
    {
        $sekarang = now();

        $data = Borrowing::with(['item', 'user'])
            ->where('status', 'dipinjam')
            ->whereNotNull('tanggal_kembali')
            ->get()
            ->filter(function ($b) use ($sekarang) {
                
                // Skip kalau sudah dinotifikasi dalam 24 jam terakhir
                if ($b->notifikasi_terkirim_at) {
                    $sudahNotif = Carbon::parse($b->notifikasi_terkirim_at);
                    if ($sudahNotif->diffInHours($sekarang) < 24) {
                        return false; // skip, jangan kirim lagi
                    }
                }

                // Kalau ada jam kembali — cek per jam
                if ($b->jam_kembali) {
                    // PERBAIKAN: Bungkus dengan Carbon::parse sebelum di-format
                    $tanggalFormat = Carbon::parse($b->tanggal_kembali)->format('Y-m-d');
                    $deadlineJam = Carbon::parse($tanggalFormat . ' ' . $b->jam_kembali);
                    
                    return $sekarang->gt($deadlineJam);
                }

                // Kalau tidak ada jam — cek per hari
                return $sekarang->startOfDay()->gt(
                    Carbon::parse($b->tanggal_kembali)->startOfDay()
                );

            })
            ->map(fn($b) => [
                'kode_peminjaman'  => $b->kode_peminjaman,
                'nama_peminjam'    => $b->user->name ?? '-',
                'email_peminjam'  => $b->user->email ?? '-',
                'no_hp'            => $b->user->no_hp ?? '-',
                'nama_barang'      => $b->item->nama_barang ?? '-',
                'jumlah_pinjam'    => $b->jumlah_pinjam,
                'tanggal_kembali'  => $b->tanggal_kembali,
                'jam_kembali'      => $b->jam_kembali ?? 'Per hari',
                // PERBAIKAN: Bungkus juga bagian format di bawah ini
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

    public function tandaiNotifikasi(Request $request)
    {
        $kode = $request->kode_peminjaman;

        Borrowing::where('kode_peminjaman', $kode)
            ->update(['notifikasi_terkirim_at' => now()]);

        return response()->json(['status' => 'ok']);
}
}