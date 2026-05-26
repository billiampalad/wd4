<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nik', 'password');

        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            $roleName = $this->normalizeRoleName($user->role?->role_name);

            if ($roleName == 'pimpinan') {
                return redirect('/pimpinan');
            }

            if ($roleName == 'jurusan') {
                return redirect('/jurusan');
            }

            if ($roleName == 'unit_kerja') {
                return redirect('/unit');
            }

            if ($roleName == 'upa') {
                return redirect('/upa');
            }

            if ($roleName == 'pusat') {
                return redirect('/pusat');
            }

            if ($roleName == 'admin') {
                return redirect('/admin');
            }
        }

        return back()->with('error', 'Login gagal');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function normalizeRoleName(?string $roleName): string
    {
        return strtolower(str_replace(' ', '_', trim((string) $roleName)));
    }
}
