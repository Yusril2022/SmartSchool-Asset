<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: "/users",
        summary: "Daftar semua user (admin)",
        description: "Menampilkan daftar semua user yang terdaftar di sistem. Hanya untuk admin.",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman daftar user"),
            new OA\Response(response: 403, description: "Akses ditolak"),
        ]
    )]
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    #[OA\Get(
        path: "/users/create",
        summary: "Form tambah user baru (admin)",
        description: "Menampilkan form untuk menambahkan user baru. Hanya untuk admin.",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Halaman form tambah user"),
        ]
    )]
    public function create()
    {
        return view('admin.users.create');
    }

    #[OA\Post(
        path: "/users",
        summary: "Simpan user baru (admin)",
        description: "Membuat akun user baru dengan role tertentu. Email dan nomor induk harus unik.",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "nomor_induk", "no_hp", "email", "password", "role"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Budi Santoso"),
                    new OA\Property(property: "nomor_induk", type: "string", example: "12345678", description: "NIS/NIP, harus unik"),
                    new OA\Property(property: "no_hp", type: "string", example: "081234567890", description: "Nomor HP (max 15 karakter)"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "budi@sekolah.sch.id"),
                    new OA\Property(property: "password", type: "string", example: "secret123", description: "Minimal 6 karakter"),
                    new OA\Property(property: "role", type: "string", enum: ["admin", "guru", "siswa"], example: "guru"),
                    new OA\Property(property: "jabatan", type: "string", enum: ["Kepala Sekolah", "Wakasek Sarana dan Prasarana"], example: "Kepala Sekolah", description: "Opsional, khusus guru"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar user jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal (email/nomor induk sudah dipakai)"),
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'nomor_induk'  => 'required|string|unique:users,nomor_induk',
            'no_hp'        => 'required|string|max:15',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'role'         => 'required|in:admin,guru,siswa',
            'jabatan'      => 'nullable|in:Kepala Sekolah,Wakasek Sarana dan Prasarana',
        ]);

        User::create([
            'name'         => $request->name,
            'nomor_induk'  => $request->nomor_induk,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
            'jabatan'      => $request->jabatan,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat');
    }

    #[OA\Get(
        path: "/users/{id}",
        summary: "Detail user (admin)",
        description: "Menampilkan detail informasi satu user. Hanya untuk admin.",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman detail user"),
            new OA\Response(response: 404, description: "User tidak ditemukan"),
        ]
    )]
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    #[OA\Get(
        path: "/users/{id}/edit",
        summary: "Form edit user (admin)",
        description: "Menampilkan form untuk mengedit data user. Hanya untuk admin.",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: "Halaman form edit user"),
            new OA\Response(response: 404, description: "User tidak ditemukan"),
        ]
    )]
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    #[OA\Put(
        path: "/users/{id}",
        summary: "Update data user (admin)",
        description: "Mengupdate data user. Password hanya diupdate jika diisi. Email dan nomor induk harus unik kecuali milik user itu sendiri.",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "nomor_induk", "no_hp", "email", "role"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Budi Santoso"),
                    new OA\Property(property: "nomor_induk", type: "string", example: "12345678"),
                    new OA\Property(property: "no_hp", type: "string", example: "081234567890"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "budi@sekolah.sch.id"),
                    new OA\Property(property: "role", type: "string", enum: ["admin", "guru", "siswa"], example: "siswa"),
                    new OA\Property(property: "jabatan", type: "string", enum: ["Kepala Sekolah", "Wakasek Sarana dan Prasarana"], description: "Opsional"),
                    new OA\Property(property: "password", type: "string", example: "newpassword", description: "Opsional, minimal 6 karakter. Kosongkan jika tidak ingin mengubah password."),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 302, description: "Redirect ke daftar user jika berhasil"),
            new OA\Response(response: 422, description: "Validasi gagal"),
            new OA\Response(response: 404, description: "User tidak ditemukan"),
        ]
    )]
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'nomor_induk' => 'required|string|unique:users,nomor_induk,' . $id,
            'no_hp'       => 'required|string|max:15',
            'email'       => 'required|email|unique:users,email,' . $id,
            'role'        => 'required|in:admin,guru,siswa',
            'jabatan'     => 'nullable|in:Kepala Sekolah,Wakasek Sarana dan Prasarana',
            'password'    => 'nullable|min:6',
        ]);

        $data = [
            'name'        => $request->name,
            'nomor_induk' => $request->nomor_induk,
            'no_hp'       => $request->no_hp,
            'email'       => $request->email,
            'role'        => $request->role,
            'jabatan'     => $request->jabatan,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    #[OA\Delete(
        path: "/users/{id}",
        summary: "Hapus user (admin)",
        description: "Menghapus user dari sistem. Admin tidak dapat menghapus akunnya sendiri.",
        tags: ["User"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer", example: 2)),
        ],
        responses: [
            new OA\Response(response: 302, description: "Redirect dengan pesan sukses atau error jika menghapus akun sendiri"),
            new OA\Response(response: 404, description: "User tidak ditemukan"),
        ]
    )]
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}