<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Cabinet;
use App\Exports\ItemExport;
use Illuminate\Http\Request;
use App\Services\ItemService;
use App\Models\Room;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class ItemController extends Controller
{
    protected $service;

    public function __construct(ItemService $service)
    {
        $this->service = $service;
    }

    // ================= ADMIN =================

    #[OA\Get(
        path: "/items",
        summary: "Daftar semua barang (admin)",
        description: "Menampilkan daftar semua barang dengan filter nama dan jenis barang. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "Cari berdasarkan nama barang",
                schema: new OA\Schema(type: "string", example: "Laptop")
            ),
            new OA\Parameter(
                name: "jenis",
                in: "query",
                required: false,
                description: "Filter jenis barang",
                schema: new OA\Schema(type: "string", enum: ["semua", "aset", "konsumsi"], example: "aset")
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar barang admin"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index(Request $request)
    {
        $query = Item::with('cabinet.room')->latest();

        if ($request->search) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->jenis && $request->jenis !== 'semua') {
            $query->where('jenis_barang', $request->jenis);
        }

        $barangs = $query->paginate(15)->withQueryString();

        return view('admin.items.index', compact('barangs'));
    }

    #[OA\Get(
        path: "/items/export",
        summary: "Export data barang ke Excel",
        description: "Download file Excel berisi data semua barang. Mendukung filter yang sama dengan halaman daftar. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "search", in: "query", required: false, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "jenis", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["semua", "aset", "konsumsi"])),
        ],
        responses: [
            new OA\Response(response: 200, description: "File Excel (.xlsx)"),
        ]
    )]
    public function export(Request $request)
    {
        $filename = 'barang-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new ItemExport($request), $filename);
    }

    #[OA\Get(
        path: "/items/create",
        summary: "Form tambah barang baru (admin)",
        description: "Menampilkan form untuk menambahkan barang baru ke sistem. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman form tambah barang"),
        ]
    )]
    public function create()
    {
        $lemaris = Cabinet::with('room')->get();
        $rooms   = Room::orderBy('nama_ruangan')->get();
        return view('admin.items.create', compact('lemaris', 'rooms'));
    }

    #[OA\Post(
        path: "/items",
        summary: "Simpan barang baru (admin)",
        description: "Menyimpan data barang baru ke sistem. Mendukung upload foto barang. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["nama_barang", "kategori", "jenis_barang", "stok_awal", "batas_minimum", "harga"],
                    properties: [
                        new OA\Property(property: "nama_barang", type: "string", example: "Laptop Dell Inspiron"),
                        new OA\Property(property: "kategori", type: "string", example: "Elektronik"),
                        new OA\Property(property: "jenis_barang", type: "string", enum: ["aset", "konsumsi"], example: "aset"),
                        new OA\Property(property: "merk", type: "string", example: "Dell"),
                        new OA\Property(property: "hasil_perolehan", type: "string", enum: ["pembelian", "hibah", "sumbangan", "dana_bos"], example: "pembelian"),
                        new OA\Property(property: "id_ruangan", type: "integer", example: 1, description: "ID ruangan (opsional, untuk filter lemari di form)"),
                        new OA\Property(property: "id_lemari", type: "integer", example: 2, description: "ID lemari tempat barang disimpan"),
                        new OA\Property(property: "stok_awal", type: "integer", example: 5),
                        new OA\Property(property: "batas_minimum", type: "integer", example: 2, description: "Batas minimum stok sebelum dianggap kritis"),
                        new OA\Property(property: "harga", type: "integer", example: 8000000, description: "Harga satuan barang dalam rupiah"),
                        new OA\Property(property: "foto", type: "string", format: "binary", description: "Foto barang (jpg/jpeg/png, max 2MB)"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar barang jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'     => 'required|string|max:255',
            'kategori'        => 'required|string|max:255',
            'jenis_barang'    => 'required|in:aset,konsumsi',
            'merk'            => 'nullable|string|max:255',
            'hasil_perolehan' => 'nullable|in:pembelian,hibah,sumbangan,dana_bos',
            'id_ruangan'      => 'nullable|exists:rooms,id',
            'id_lemari'       => 'nullable|exists:cabinets,id',
            'stok_awal'       => 'required|integer|min:0',
            'batas_minimum'   => 'required|integer|min:0',
            'harga'           => 'required|integer|min:0',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $data = $request->except(['_token', '_method']);
            $this->service->store($data, $request->file('foto'));

            return redirect()->route('items.index')
                ->with('success', 'Barang berhasil ditambahkan');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    #[OA\Get(
        path: "/items/{id}/edit",
        summary: "Form edit barang (admin)",
        description: "Menampilkan form untuk mengedit data barang beserta riwayat kondisi barang. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID barang",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman form edit barang"),
            new OA\Response(response: 404, description: "Barang tidak ditemukan"),
        ]
    )]
    public function edit($id)
    {
        $barang  = Item::with('conditionLogs.admin')->findOrFail($id);
        $lemaris = Cabinet::with('room')->get();
        $rooms   = Room::orderBy('nama_ruangan')->get();
        return view('admin.items.edit', compact('barang', 'lemaris', 'rooms'));
    }

    #[OA\Put(
        path: "/items/{id}",
        summary: "Update data barang (admin)",
        description: "Mengupdate data barang yang sudah ada. Foto lama tetap dipertahankan jika tidak ada foto baru yang diupload. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID barang",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["nama_barang", "kategori", "jenis_barang", "batas_minimum", "harga"],
                    properties: [
                        new OA\Property(property: "nama_barang", type: "string", example: "Laptop Dell Inspiron"),
                        new OA\Property(property: "kategori", type: "string", example: "Elektronik"),
                        new OA\Property(property: "jenis_barang", type: "string", enum: ["aset", "konsumsi"], example: "aset"),
                        new OA\Property(property: "id_lemari", type: "integer", example: 2),
                        new OA\Property(property: "batas_minimum", type: "integer", example: 2),
                        new OA\Property(property: "harga", type: "integer", example: 8000000),
                        new OA\Property(property: "kondisi", type: "string", enum: ["Baik", "Rusak Ringan", "Rusak Sedang", "Rusak Berat", "Mati Total"], example: "Baik"),
                        new OA\Property(property: "foto", type: "string", format: "binary", description: "Foto baru (opsional)"),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar barang jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "Barang tidak ditemukan"),
        ]
    )]
    public function update(Request $request, $id)
    {
        $barang = Item::findOrFail($id);

        $request->validate([
            'nama_barang'   => 'required|string|max:255',
            'kategori'      => 'required|string|max:255',
            'jenis_barang'  => 'required|in:aset,konsumsi',
            'id_lemari'     => 'nullable|exists:cabinets,id',
            'batas_minimum' => 'required|integer|min:0',
            'harga'         => 'required|integer|min:0',
            'kondisi'       => 'nullable|in:Baik,Rusak Ringan,Rusak Sedang,Rusak Berat,Mati Total',
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $data = $request->except(['_token', '_method']);
            $this->service->update($barang, $data, $request->file('foto'));

            return redirect()->route('items.index')
                ->with('success', 'Barang berhasil diupdate');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    #[OA\Delete(
        path: "/items/{id}",
        summary: "Hapus barang (admin)",
        description: "Menghapus data barang dari sistem. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID barang yang akan dihapus",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 302, description: "Redirect dengan pesan sukses"),
            new OA\Response(response: 404, description: "Barang tidak ditemukan"),
        ]
    )]
    public function destroy($id)
    {
        $barang = Item::findOrFail($id);
        $this->service->delete($barang);
        return back()->with('success', 'Barang berhasil dihapus');
    }

    #[OA\Get(
        path: "/items/{id}/qr",
        summary: "Download QR Code barang (admin)",
        description: "Menampilkan halaman cetak QR Code untuk barang tertentu. Hanya untuk admin.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID barang",
                schema: new OA\Schema(type: "integer", example: 1)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman cetak QR Code"),
            new OA\Response(response: 404, description: "Barang tidak ditemukan"),
        ]
    )]
    public function downloadQr($id)
    {
        $barang = Item::findOrFail($id);
        return view('admin.items.qr-print', compact('barang'));
    }

    // ================= USER =================

    #[OA\Get(
        path: "/catalog",
        summary: "Katalog barang untuk user (siswa/guru)",
        description: "Menampilkan katalog semua barang yang bisa dilihat oleh siswa dan guru. Mendukung pencarian nama barang.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "search",
                in: "query",
                required: false,
                description: "Cari berdasarkan nama barang",
                schema: new OA\Schema(type: "string", example: "Proyektor")
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman katalog barang"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function userIndex(Request $request)
    {
        $query = Item::with('cabinet.room');

        if ($request->search) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        $barangs = $query->latest()->get();

        return view('user.items.index', compact('barangs'));
    }

    #[OA\Get(
        path: "/catalog/{id}",
        summary: "Detail barang untuk user (siswa/guru)",
        description: "Menampilkan detail satu barang termasuk lokasi penyimpanannya.",
        tags: ["Barang"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID barang",
                schema: new OA\Schema(type: "integer", example: 5)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman detail barang"),
            new OA\Response(response: 404, description: "Barang tidak ditemukan"),
        ]
    )]
    public function showUser($id)
    {
        $barang = Item::with('cabinet.room')->findOrFail($id);
        return view('user.items.show', compact('barang'));
    }

    // ================= SCAN =================

    #[OA\Get(
        path: "/scan/{kode}",
        summary: "Scan QR Code barang (publik)",
        description: "Menampilkan detail barang berdasarkan kode QR yang di-scan. Dapat diakses tanpa login.",
        tags: ["Barang"],
        parameters: [
            new OA\Parameter(
                name: "kode",
                in: "path",
                required: true,
                description: "Kode unik barang yang tertera pada QR Code",
                schema: new OA\Schema(type: "string", example: "BRG-001")
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman detail barang hasil scan"),
            new OA\Response(response: 404, description: "Barang dengan kode tersebut tidak ditemukan"),
        ]
    )]
    public function scan($kode)
    {
        $barang = Item::with('cabinet.room')->where('kode_barang', $kode)->first();

        if (!$barang) {
            abort(404, 'Barang dengan kode ' . $kode . ' tidak ditemukan.');
        }

        return view('scan.detail', compact('barang'));
    }
}