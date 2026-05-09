<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Cabinet;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    public function store($data, $foto = null)
    {
        $fotoPath = null;
        if ($foto) {
            $fotoPath = $foto->store('items', 'public');
        }

        // Tentukan id_lemari:
        // Kalau user pilih lemari        → pakai itu
        // Kalau skip lemari + pilih ruangan → auto-create cabinet "Tanpa Lemari"
        // Kalau keduanya skip            → null
        $idLemari = null;

        if (!empty($data['id_lemari'])) {
            $idLemari = $data['id_lemari'];
        } elseif (!empty($data['id_ruangan'])) {
            $cabinet = Cabinet::firstOrCreate(
                [
                    'id_ruangan'  => $data['id_ruangan'],
                    'nama_lemari' => 'Tanpa Lemari',
                ],
                [
                    // kode_lemari unik per ruangan: TL-{id_ruangan}
                    'kode_lemari' => 'TL-' . $data['id_ruangan'],
                ]
            );
            $idLemari = $cabinet->id;
        }

        return Item::create([
            'kode_barang'     => $data['kode_barang'] ?? 'BRG-' . strtoupper(uniqid()),
            'nama_barang'     => $data['nama_barang'],
            'kategori'        => $data['kategori'],
            'stok_awal'       => $data['stok_awal'],
            'stok_total'      => $data['stok_awal'],
            'id_lemari'       => $idLemari,
            'id_ruangan'      => $data['id_ruangan'] ?? null,
            'jenis_barang'    => $data['jenis_barang'],
            'merk'            => $data['merk'] ?? null,
            'hasil_perolehan' => $data['hasil_perolehan'] ?? null,
            'batas_minimum'   => $data['batas_minimum'] ?? 0,
            'harga'           => $data['harga'] ?? 0,
            'kondisi'         => $data['kondisi'] ?? 'Baik',
            'foto'            => $fotoPath,
        ]);
    }

    public function update($barang, $data, $foto = null)
    {
        if ($foto) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }
            $data['foto'] = $foto->store('items', 'public');
        }

        unset($data['stok_total'], $data['stok_awal'], $data['id_ruangan']);

        $barang->update($data);
        return $barang;
    }

    public function delete($barang)
    {
        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }

        return $barang->delete();
    }

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