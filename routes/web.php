<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| LANDING PAGE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| LOGIN USER
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

/*
|--------------------------------------------------------------------------
| LOGIN ADMIN
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AdminAuthController::class, 'loginForm']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| USER DASHBOARD (HARUS LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:pimpinan'])->group(function () {

    Route::get('/pimpinan', [DashboardController::class, 'pimpinan']);
});

Route::middleware(['auth', 'role:jurusan'])->group(function () {

    Route::get('/jurusan', [DashboardController::class, 'jurusan']);
});

Route::middleware(['auth', 'role:unit_kerja'])->group(function () {

    Route::get('/unit', [DashboardController::class, 'unit']);
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL (WAJIB LOGIN)
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->middleware(['auth', 'role:admin']);

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::resource('users', UserController::class);
});
