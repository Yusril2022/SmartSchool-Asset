<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Room;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CabinetController extends Controller
{
    #[OA\Get(
        path: "/cabinets",
        summary: "Daftar semua lemari (admin)",
        description: "Menampilkan daftar semua lemari beserta ruangan tempatnya berada. Hanya untuk admin.",
        tags: ["Lemari"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar lemari"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index()
    {
        $lemaris = Cabinet::with('room')->get();
        return view('admin.cabinets.index', compact('lemaris'));
    }

    #[OA\Get(
        path: "/cabinets/create",
        summary: "Form tambah lemari (admin)",
        description: "Menampilkan form untuk menambahkan lemari baru. Hanya untuk admin.",
        tags: ["Lemari"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman form tambah lemari"),
        ]
    )]
    public function create()
    {
        $ruangans = Room::all();
        return view('admin.cabinets.create', compact('ruangans'));
    }

    #[OA\Post(
        path: "/cabinets",
        summary: "Simpan lemari baru (admin)",
        description: "Menyimpan data lemari baru ke sistem. Kode lemari harus unik dan ruangan wajib dipilih.",
        tags: ["Lemari"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kode_lemari", "nama_lemari", "id_ruangan"],
                properties: [
                    new OA\Property(property: "kode_lemari", type: "string", example: "L-01", description: "Kode unik lemari"),
                    new OA\Property(property: "nama_lemari", type: "string", example: "Lemari Elektronik"),
                    new OA\Property(property: "id_ruangan", type: "integer", example: 1, description: "ID ruangan tempat lemari berada"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar lemari jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal (kode sudah dipakai atau ruangan tidak ada)"),
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'kode_lemari' => 'required|unique:cabinets,kode_lemari',
            'nama_lemari' => 'required',
            'id_ruangan'  => 'required|exists:rooms,id',
        ]);

        Cabinet::create([
            'kode_lemari' => $request->kode_lemari,
            'nama_lemari' => $request->nama_lemari,
            'id_ruangan'  => $request->id_ruangan,
        ]);

        return redirect()->route('cabinets.index')
            ->with('success', 'Lemari berhasil ditambahkan');
    }

    #[OA\Get(
        path: "/cabinets/{id}/edit",
        summary: "Form edit lemari (admin)",
        description: "Menampilkan form untuk mengedit data lemari. Hanya untuk admin.",
        tags: ["Lemari"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman form edit lemari"),
            new OA\Response(response: 404, description: "Lemari tidak ditemukan"),
        ]
    )]
    public function edit($id)
    {
        $lemari   = Cabinet::findOrFail($id);
        $ruangans = Room::all();
        return view('admin.cabinets.edit', compact('lemari', 'ruangans'));
    }

    #[OA\Put(
        path: "/cabinets/{id}",
        summary: "Update data lemari (admin)",
        description: "Mengupdate data lemari. Kode lemari harus unik kecuali milik lemari itu sendiri.",
        tags: ["Lemari"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kode_lemari", "nama_lemari", "id_ruangan"],
                properties: [
                    new OA\Property(property: "kode_lemari", type: "string", example: "L-01"),
                    new OA\Property(property: "nama_lemari", type: "string", example: "Lemari ATK"),
                    new OA\Property(property: "id_ruangan", type: "integer", example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar lemari jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "Lemari tidak ditemukan"),
        ]
    )]
    public function update(Request $request, $id)
    {
        $cabinet = Cabinet::findOrFail($id);

        $request->validate([
            'kode_lemari' => 'required|unique:cabinets,kode_lemari,' . $id,
            'nama_lemari' => 'required|string|max:255',
            'id_ruangan'  => 'required|exists:rooms,id',
        ]);

        $cabinet->update([
            'kode_lemari' => $request->kode_lemari,
            'nama_lemari' => $request->nama_lemari,
            'id_ruangan'  => $request->id_ruangan,
        ]);

        return redirect()->route('cabinets.index')
            ->with('success', 'Lemari berhasil diupdate');
    }

    #[OA\Delete(
        path: "/cabinets/{id}",
        summary: "Hapus lemari (admin)",
        description: "Menghapus data lemari dari sistem. Hanya untuk admin.",
        tags: ["Lemari"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 302, description: "Redirect dengan pesan sukses"),
            new OA\Response(response: 404, description: "Lemari tidak ditemukan"),
        ]
    )]
    public function destroy(string $id)
    {
        Cabinet::findOrFail($id)->delete();
        return back()->with('success', 'Lemari berhasil dihapus');
    }
}