<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Cek apakah role user ada di dalam daftar role yang diizinkan
        // Ini support single role:  'role:admin'
        // Maupun multi role:        'role:siswa,guru'
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}