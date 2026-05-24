<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (Auth::user()->role->role_name !== $role) {
            $dashboardRoute = match (Auth::user()->role?->role_name) {
                'pimpinan' => 'pimpinan.dashboard',
                'jurusan' => 'jurusan.dashboard',
                'unit_kerja' => 'unit.dashboard',
                'admin' => 'admin.dashboard',
                default => null,
            };

            if ($dashboardRoute && \Illuminate\Support\Facades\Route::has($dashboardRoute)) {
                return redirect()->route($dashboardRoute)->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }

            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
