<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class AdminAuthController
{
    private const MAX_LOGIN_ATTEMPTS = 3;
    private const LOGIN_LOCKOUT_SECONDS = 60;

    public function loginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('nik','password');
        $throttleKey = $this->throttleKey($request);
        $lockoutKey = $this->lockoutKey($request);

        if ($lockoutSeconds = $this->lockoutSeconds($lockoutKey)) {
            return $this->lockoutResponse($request, $lockoutSeconds);
        }

        if(Auth::attempt($credentials)){

            if (strtolower(trim((string) Auth::user()->role?->role_name)) === 'admin') {
                RateLimiter::clear($throttleKey);
                Cache::forget($lockoutKey);

                return redirect('/admin/dashboard')->with('success', 'Berhasil masuk ke sistem admin.');
            }

            Auth::logout();

            return $this->failedLoginResponse(
                $request,
                $throttleKey,
                $lockoutKey,
                'Akun ini tidak memiliki akses admin.'
            );
        }

        return $this->failedLoginResponse($request, $throttleKey, $lockoutKey);
    }

    private function failedLoginResponse(
        Request $request,
        string $throttleKey,
        string $lockoutKey,
        string $message = 'NIK atau kata sandi salah.'
    ) {
        RateLimiter::hit($throttleKey, self::LOGIN_LOCKOUT_SECONDS);

        $remainingAttempts = RateLimiter::remaining($throttleKey, self::MAX_LOGIN_ATTEMPTS);

        if ($remainingAttempts <= 0) {
            RateLimiter::clear($throttleKey);
            $this->activateLockout($lockoutKey);

            return $this->lockoutResponse($request, self::LOGIN_LOCKOUT_SECONDS, $message);
        }

        return back()
            ->with('error', "{$message} Sisa percobaan: {$remainingAttempts} kali.")
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

    private function throttleKey(Request $request): string
    {
        return 'admin-login-attempts|' . $request->ip();
    }

    private function lockoutKey(Request $request): string
    {
        return 'admin-login-lockout|' . $request->ip();
    }
}