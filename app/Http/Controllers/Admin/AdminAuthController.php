<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController
{
    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nik','password');

        if(Auth::attempt($credentials)){

            if (strtolower(trim((string) Auth::user()->role?->role_name)) === 'admin') {
                return redirect('/admin/dashboard');
            }

            Auth::logout();
        }

        return back()->with('error','Bukan Admin');
    }
}
