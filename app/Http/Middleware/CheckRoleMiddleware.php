<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Ambil role user asli
        $userRole = $request->user()->role;
        
        // Cek apakah user memiliki role yang diizinkan
        // Untuk kompatibilitas, jika route membutuhkan 'peminjam', user 'siswa' diizinkan
        if (in_array('peminjam', $roles) && $userRole === 'siswa') {
            return $next($request);
        }
        
        // Cek langsung apakah role user ada di daftar yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access');
    }
}