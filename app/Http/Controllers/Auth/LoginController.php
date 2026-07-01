<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class LoginController
{
    private const MAX_LOGIN_ATTEMPTS = 3;
    private const LOGIN_LOCKOUT_SECONDS = 60;

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nik', 'password');
        $throttleKey = $this->throttleKey($request);
        $lockoutKey = $this->lockoutKey($request);

        if ($lockoutSeconds = $this->lockoutSeconds($lockoutKey)) {
            return $this->lockoutResponse($request, $lockoutSeconds);
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            Cache::forget($lockoutKey);

            $user = Auth::user();
            $roleName = $this->normalizeRoleName($user->role?->role_name);

            if ($roleName == 'pimpinan') {
                return redirect('/pimpinan')->with('success', 'Berhasil masuk ke sistem.');
            }

            if ($roleName == 'jurusan') {
                return redirect('/jurusan')->with('success', 'Berhasil masuk ke sistem.');
            }

            if ($roleName == 'unit_kerja') {
                return redirect('/unit')->with('success', 'Berhasil masuk ke sistem.');
            }

            if ($roleName == 'upa') {
                return redirect('/upa')->with('success', 'Berhasil masuk ke sistem.');
            }

            if ($roleName == 'pusat') {
                return redirect('/pusat')->with('success', 'Berhasil masuk ke sistem.');
            }

            if ($roleName == 'admin') {
                return redirect('/admin')->with('success', 'Berhasil masuk ke sistem.');
            }
        }

        return $this->failedLoginResponse($request, $throttleKey, $lockoutKey);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function heartbeat()
    {
        return response()->noContent();
    }

    private function failedLoginResponse(Request $request, string $throttleKey, string $lockoutKey)
    {
        RateLimiter::hit($throttleKey, self::LOGIN_LOCKOUT_SECONDS);

        $remainingAttempts = RateLimiter::remaining($throttleKey, self::MAX_LOGIN_ATTEMPTS);

        if ($remainingAttempts <= 0) {
            RateLimiter::clear($throttleKey);
            $this->activateLockout($lockoutKey);

            return $this->lockoutResponse($request, self::LOGIN_LOCKOUT_SECONDS, 'NIK atau kata sandi salah.');
        }

        return back()
            ->with('error', "NIK atau kata sandi salah. Sisa percobaan: {$remainingAttempts} kali.")
            ->withInput($request->only('nik'));
    }

    private function activateLockout(string $lockoutKey): void
    {
        Cache::put($lockoutKey, now()->addSeconds(self::LOGIN_LOCKOUT_SECONDS)->timestamp, self::LOGIN_LOCKOUT_SECONDS);
    }

    private function lockoutSeconds(string $lockoutKey): int
    {
        $expiresAt = (int) Cache::get($lockoutKey, 0);

        if ($expiresAt <= 0) {
            return 0;
        }

        $seconds = $expiresAt - now()->timestamp;

        if ($seconds <= 0) {
            Cache::forget($lockoutKey);

            return 0;
        }

        return $seconds;
    }

    private function lockoutResponse(Request $request, int $seconds, string $prefix = 'Login sedang dikunci.')
    {
        return back()
            ->with('error', "{$prefix} Silakan coba lagi dalam {$seconds} detik.")
            ->with('lockout_seconds', $seconds)
            ->withInput($request->only('nik'));
    }

    private function normalizeRoleName(?string $roleName): string
    {
        $normalizedRole = strtolower(str_replace([' ', '-'], '_', trim((string) $roleName)));

        return $normalizedRole === 'humas' ? 'unit_kerja' : $normalizedRole;
    }

    private function throttleKey(Request $request): string
    {
        return 'login-attempts|' . strtolower((string) $request->input('nik')) . '|' . $request->ip();
    }

    private function lockoutKey(Request $request): string
    {
        return 'login-lockout|' . strtolower((string) $request->input('nik')) . '|' . $request->ip();
    }
}