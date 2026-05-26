<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Borrowing;
use App\Models\Item;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share data notifikasi ke semua view yang pakai layouts.admin
        View::composer('layouts.admin', function ($view) {

            // Hanya untuk admin yang sudah login
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                $view->with('notifData', collect());
                $view->with('notifCount', 0);
                return;
            }

            $notifs = collect();

            // 1. Peminjaman pending
            $pending = Borrowing::with(['user', 'item'])
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            foreach ($pending as $p) {
                $notifs->push([
                    'type'  => 'pending',
                    'icon'  => '⏳',
                    'color' => 'text-yellow-600',
                    'bg'    => 'bg-yellow-50',
                    'text'  => ($p->user->name ?? '-') . ' mengajukan pinjam ' . ($p->item->nama_barang ?? '-'),
                    'time'  => $p->created_at->diffForHumans(),
                    'url'   => route('admin.borrowings.show', $p->id),
                ]);
            }

            // 2. Terlambat dikembalikan
            $terlambat = Borrowing::with(['user', 'item'])
                ->where('status', 'dipinjam')
                ->whereNotNull('tanggal_kembali')
                ->where('tanggal_kembali', '<', now())
                ->latest()
                ->take(5)
                ->get();

            foreach ($terlambat as $t) {
                $notifs->push([
                    'type'  => 'terlambat',
                    'icon'  => '🚨',
                    'color' => 'text-red-600',
                    'bg'    => 'bg-red-50',
                    'text'  => ($t->user->name ?? '-') . ' terlambat kembalikan ' . ($t->item->nama_barang ?? '-'),
                    'time'  => \Carbon\Carbon::parse($t->tanggal_kembali)->diffForHumans(),
                    'url'   => route('admin.borrowings.show', $t->id),
                ]);
            }

            // 3. Stok kritis
            $kritis = Item::stokKritis()->take(3)->get();
            foreach ($kritis as $k) {
                $notifs->push([
                    'type'  => 'stok',
                    'icon'  => '⚠️',
                    'color' => 'text-orange-600',
                    'bg'    => 'bg-orange-50',
                    'text'  => 'Stok ' . $k->nama_barang . ' kritis (' . $k->stok_total . ' tersisa)',
                    'time'  => '',
                    'url'   => route('items.edit', $k->id),
                ]);
            }

            $view->with('notifData', $notifs);
            $view->with('notifCount', $notifs->count());
        });
    }
}