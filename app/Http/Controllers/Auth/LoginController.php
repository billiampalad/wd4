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

            if ($user->role->role_name == 'pimpinan') {
                return redirect('/pimpinan');
            }

            if ($user->role->role_name == 'jurusan') {
                return redirect('/jurusan');
            }

            if ($user->role->role_name == 'unit_kerja') {
                return redirect('/unit');
            }

            if ($user->role->role_name == 'admin') {
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
}
