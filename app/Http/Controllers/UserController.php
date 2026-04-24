<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'nomor_induk'  => 'required|string|unique:users,nomor_induk',
            'no_hp'        => 'required|string|max:15',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:6',
            'role'         => 'required|in:admin,guru,siswa', // ✅ validasi role
        ]);

        User::create([
            'name'         => $request->name,
            'nomor_induk'  => $request->nomor_induk,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role, 
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'nomor_induk' => 'required|string|unique:users,nomor_induk,' . $id,
            'no_hp'       => 'required|string|max:15',
            'email'       => 'required|email|unique:users,email,' . $id,
            'role'        => 'required|in:admin,guru,siswa',
            // password opsional saat update
            'password'    => 'nullable|min:6',
        ]);

        $data = [
            'name'        => $request->name,
            'nomor_induk' => $request->nomor_induk,
            'no_hp'       => $request->no_hp,
            'email'       => $request->email,
            'role'        => $request->role,
        ];

        // Hanya update password kalau diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }
}