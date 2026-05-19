<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-API-Token');

        if (!$token || $token !== config('services.n8n.token')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}