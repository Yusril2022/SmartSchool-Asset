<?php

namespace App\Services;

use App\Models\Item;

class BarangService
{
    public function store($data)
    {
        return Item::create([
            'kode_barang' => $data['kode_barang'] ?? 'BRG-' . time(),
            'nama_barang' => $data['nama_barang'],
            'kategori' => $data['kategori'],
            'stok_awal' => $data['stok_awal'],
            'stok_total' => $data['stok_awal'],
            'id_lemari' => $data['id_lemari'],
            'jenis_barang' => $data['jenis_barang'],
            'batas_minimum' => $data['batas_minimum'] ?? 0,
            'harga' => $data['harga'] ?? 0,
        ]);
    }

    public function update($barang, $data)
    {
        $barang->update($data);
        return $barang;
    }

    public function delete($barang)
    {
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
