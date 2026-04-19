<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Room;
use Illuminate\Http\Request;

class LemariController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 🔥 GANTI KE CABINET + ROOM
        $lemaris = Cabinet::with('room')->get();

        return view('admin.lemari.index', compact('lemaris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 🔥 GANTI KE ROOM
        $ruangans = Room::all();

        return view('admin.lemari.create', compact('ruangans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_lemari' => 'required|unique:cabinets,kode_lemari', // 🔥 FIX TABLE
            'nama_lemari' => 'required',
            'id_ruangan' => 'required|exists:rooms,id', // 🔥 FIX FIELD
        ]);

        // 🔥 JANGAN PAKAI $request->all()
        Cabinet::create([
            'kode_lemari' => $request->kode_lemari,
            'nama_lemari' => $request->nama_lemari,
            'id_ruangan' => $request->id_ruangan,
        ]);

        return redirect()->route('lemari.index')
            ->with('success', 'Lemari berhasil ditambahkan');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Cabinet::findOrFail($id)->delete();

        return back()->with('success', 'Lemari berhasil dihapus');
    }

    public function edit($id)
    {
        $lemari = Cabinet::findOrFail($id);
        $ruangans = Room::all();

        return view('admin.lemari.edit', compact('lemari', 'ruangans'));
    }
}
