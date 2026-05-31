<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Item;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AssetPositionController extends Controller
{
    #[OA\Get(
        path: "/asset-position",
        summary: "Laporan posisi aset per ruangan (admin)",
        description: "Menampilkan halaman laporan posisi aset. Mengelompokkan barang aset berdasarkan ruangan dan lemari. Hanya untuk admin.",
        tags: ["Posisi Aset"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman laporan posisi aset"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index()
    {
        $rooms = Room::with(['cabinets.items' => function ($q) {
            $q->where('jenis_barang', 'aset');
        }])->get();

        return view('admin.reports.asset-position', compact('rooms'));
    }

    #[OA\Get(
        path: "/asset-position/print",
        summary: "Cetak laporan posisi aset (admin)",
        description: "Menampilkan halaman cetak laporan posisi aset. Dapat difilter berdasarkan ruangan tertentu. Jika tidak memilih ruangan, semua ruangan yang memiliki aset akan ditampilkan.",
        tags: ["Posisi Aset"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "room_ids[]",
                in: "query",
                required: false,
                description: "ID ruangan yang ingin dicetak. Bisa pilih lebih dari satu. Kosongkan untuk semua ruangan.",
                schema: new OA\Schema(
                    type: "array",
                    items: new OA\Items(type: "integer"),
                    example: [1, 2, 3]
                )
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman cetak laporan posisi aset"),
            new OA\Response(response: 422, description: "Validasi gagal (room_ids tidak valid)"),
        ]
    )]
    public function print(Request $request)
    {
        $request->validate([
            'room_ids'    => 'nullable|array',
            'room_ids.*'  => 'exists:rooms,id',
        ]);

        $query = Room::with(['cabinets' => function ($q) {
            $q->with(['items' => function ($q2) {
                $q2->where('jenis_barang', 'aset')
                   ->orderBy('nama_barang');
            }]);
        }]);

        if ($request->filled('room_ids')) {
            $query->whereIn('id', $request->room_ids);
        }

        $rooms = $query->orderBy('nama_ruangan')->get();

        $rooms = $rooms->filter(function ($room) {
            return $room->cabinets->some(function ($cabinet) {
                return $cabinet->items->isNotEmpty();
            });
        });

        return view('admin.reports.asset-position-print', compact('rooms'));
    }
}