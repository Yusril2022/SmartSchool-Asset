<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    public function store($data, $foto = null)
    {
        $fotoPath = null;
        if ($foto) {
        
            $fotoPath = $foto->store('items', 'public');
        }

        return Item::create([
            'kode_barang'   => $data['kode_barang'] ?? 'ITM-' . strtoupper(uniqid()),
            'nama_barang'   => $data['nama_barang'],
            'kategori'      => $data['kategori'],
            'stok_awal'     => $data['stok_awal'],
            'id_lemari'     => $data['id_lemari'],
            'jenis_barang'  => $data['jenis_barang'],
            'batas_minimum' => $data['batas_minimum'] ?? 0,
            'harga'         => $data['harga'] ?? 0,
            'foto'          => $fotoPath,  // ← tambah ini
        ]);
    }


    public function update($barang, $data, $foto = null)  // ← tambah parameter foto
    {
        if ($foto) {
            // hapus foto lama kalau ada
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            // simpan foto baru
            $data['foto'] = $foto->store('items', 'public');
        }

        // pastikan stok_total tidak bisa diubah dari sini
        unset($data['stok_total']);

        $barang->update($data);
        return $barang;
    }

    public function delete($barang)
    {
        // hapus foto dari storage sebelum hapus data
        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }

        return $barang->delete();
    }

    // 🔥 tambahan logic penting
    public function tambahStok($barang, $jumlah)
    {
        $barang->increment('stok_total', $jumlah);
    }

    public function kurangiStok($barang, $jumlah)
    {
        if ($barang->stok_total < $jumlah) {
            throw new \Exception('Stok tidak cukup');
        }

        $barang->decrement('stok_total', $jumlah);
    }
}