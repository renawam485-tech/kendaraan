<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Cek 1: Apakah user sudah login?
        if (! $request->user()) {
            return redirect('login');
        }

        // Cek 2: Apakah role user sesuai dengan yang diminta?
        // Kita bisa mengirim banyak role dipisah koma, misal: 'admin_ga,approver'
        $roles = explode(',', $role);

        if (! in_array($request->user()->role, $roles)) {
            // Jika tidak sesuai, tampilkan Error 403 (Forbidden)
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}