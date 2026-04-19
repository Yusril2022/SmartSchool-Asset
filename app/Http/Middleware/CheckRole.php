<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // cek apakah user login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // cek role
        if (auth()->user()->role !== $role) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}