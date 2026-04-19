<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class LokasiRuanganController extends Controller
{
    public function index()
    {
        $ruangans = Room::latest()->get();

        return view('admin.lokasi-ruangan.index', compact('ruangans'));
    }

    public function create()
    {
        return view('admin.lokasi-ruangan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:rooms,kode_ruangan',
            'nama_ruangan' => 'required'
        ]);

        Room::create([
            'kode_ruangan' => $request->kode_ruangan,
            'nama_ruangan' => $request->nama_ruangan,
        ]);

        return redirect()->route('lokasi-ruangan.index')
            ->with('success', 'Ruangan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $ruangan = Room::findOrFail($id);

        return view('admin.lokasi-ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, $id)
    {
        $ruangan = Room::findOrFail($id);

        $request->validate([
            'kode_ruangan' => 'required|unique:rooms,kode_ruangan,' . $id,
            'nama_ruangan' => 'required'
        ]);

        $ruangan->update([
            'kode_ruangan' => $request->kode_ruangan,
            'nama_ruangan' => $request->nama_ruangan,
        ]);

        return redirect()->route('lokasi-ruangan.index')
            ->with('success', 'Ruangan berhasil diupdate');
    }

    public function destroy($id)
    {
        Room::findOrFail($id)->delete();

        return redirect()->route('lokasi-ruangan.index')
            ->with('success', 'Ruangan berhasil dihapus');
    }
} 