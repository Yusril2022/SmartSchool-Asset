<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RoomController extends Controller
{
    #[OA\Get(
        path: "/rooms",
        summary: "Daftar semua ruangan (admin)",
        description: "Menampilkan daftar semua ruangan yang terdaftar di sistem. Hanya untuk admin.",
        tags: ["Ruangan"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar ruangan"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index()
    {
        $ruangans = Room::latest()->get();
        return view('admin.rooms.index', compact('ruangans'));
    }

    #[OA\Get(
        path: "/rooms/create",
        summary: "Form tambah ruangan (admin)",
        description: "Menampilkan form untuk menambahkan ruangan baru. Hanya untuk admin.",
        tags: ["Ruangan"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman form tambah ruangan"),
        ]
    )]
    public function create()
    {
        return view('admin.rooms.create');
    }

    #[OA\Post(
        path: "/rooms",
        summary: "Simpan ruangan baru (admin)",
        description: "Menyimpan data ruangan baru ke sistem. Kode ruangan harus unik.",
        tags: ["Ruangan"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kode_ruangan", "nama_ruangan"],
                properties: [
                    new OA\Property(property: "kode_ruangan", type: "string", example: "R-01", description: "Kode unik ruangan"),
                    new OA\Property(property: "nama_ruangan", type: "string", example: "Ruang Guru"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar ruangan jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal (kode sudah dipakai)"),
        ]
    )]
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

        return redirect()->route('rooms.index')
            ->with('success', 'Ruangan berhasil ditambahkan');
    }

    #[OA\Get(
        path: "/rooms/{id}/edit",
        summary: "Form edit ruangan (admin)",
        description: "Menampilkan form untuk mengedit data ruangan. Hanya untuk admin.",
        tags: ["Ruangan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman form edit ruangan"),
            new OA\Response(response: 404, description: "Ruangan tidak ditemukan"),
        ]
    )]
    public function edit($id)
    {
        $ruangan = Room::findOrFail($id);
        return view('admin.rooms.edit', compact('ruangan'));
    }

    #[OA\Put(
        path: "/rooms/{id}",
        summary: "Update data ruangan (admin)",
        description: "Mengupdate data ruangan. Kode ruangan harus unik kecuali milik ruangan itu sendiri.",
        tags: ["Ruangan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kode_ruangan", "nama_ruangan"],
                properties: [
                    new OA\Property(property: "kode_ruangan", type: "string", example: "R-01"),
                    new OA\Property(property: "nama_ruangan", type: "string", example: "Ruang Kepala Sekolah"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar ruangan jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "Ruangan tidak ditemukan"),
        ]
    )]
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

        return redirect()->route('rooms.index')
            ->with('success', 'Ruangan berhasil diupdate');
    }

    #[OA\Delete(
        path: "/rooms/{id}",
        summary: "Hapus ruangan (admin)",
        description: "Menghapus data ruangan dari sistem. Hanya untuk admin.",
        tags: ["Ruangan"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 302, description: "Redirect dengan pesan sukses"),
            new OA\Response(response: 404, description: "Ruangan tidak ditemukan"),
        ]
    )]
    public function destroy($id)
    {
        Room::findOrFail($id)->delete();
        return redirect()->route('rooms.index')
            ->with('success', 'Ruangan berhasil dihapus');
    }
}