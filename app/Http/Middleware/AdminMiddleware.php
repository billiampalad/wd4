<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (strtolower(trim((string) Auth::user()->role?->role_name)) === 'admin') {
            return $next($request);
        }

        Auth::logout();
        return redirect()->route('login')->with('error', 'Akses ditolak.');
    }
}