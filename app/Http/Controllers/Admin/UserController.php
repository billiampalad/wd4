<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Jurusan;
use App\Models\Pusat;
use App\Models\UnitKerja;
use App\Models\Upa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role', 'profile.jurusan', 'profile.unitKerja', 'profile.upa', 'profile.pusat'])->latest()->get();
        return view('admin.layout.users', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $jurusans = Jurusan::all();
        $unitKerjas = UnitKerja::all();
        $upas = Upa::orderBy('nama_upa')->get();
        $pusats = Pusat::orderBy('nama_pusat')->get();
        return view('admin.users.create', compact('roles', 'jurusans', 'unitKerjas', 'upas', 'pusats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:255', 'unique:users,nik'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $profileData = $this->profileDataForRole($request);

        $user = User::create([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'email_verified_at' => filled($validated['email'] ?? null) ? now() : null,
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        $user->profile()->create($profileData);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['profile.jurusan', 'profile.unitKerja', 'profile.upa', 'profile.pusat'])->findOrFail($id);
        $roles = Role::all();
        $jurusans = Jurusan::all();
        $unitKerjas = UnitKerja::all();
        $upas = Upa::orderBy('nama_upa')->get();
        $pusats = Pusat::orderBy('nama_pusat')->get();

        return view('admin.users.edit', compact('user', 'roles', 'jurusans', 'unitKerjas', 'upas', 'pusats'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'string', 'max:255', Rule::unique('users', 'nik')->ignore($user->id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $profileData = $this->profileDataForRole($request);

        $newEmail = $validated['email'] ?? null;
        $emailChanged = $newEmail !== $user->email;
        $userData = [
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'email' => $newEmail,
            'role_id' => $validated['role_id'],
        ];
        if ($emailChanged) {
            $userData['email_verified_at'] = filled($newEmail) ? now() : null;
        }
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::destroy($id);
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function profileDataForRole(Request $request): array
    {
        $roleName = Role::whereKey($request->role_id)->value('role_name');
        $roleName = strtolower(str_replace(' ', '_', (string) $roleName));

        return [
            'jabatan' => $request->jabatan,
            'jurusan_id' => $roleName === 'jurusan' ? $request->jurusan_id : null,
            'unit_kerja_id' => $roleName === 'unit_kerja' ? $request->unit_kerja_id : null,
            'upa_id' => $roleName === 'upa' ? $request->upa_id : null,
            'pusat_id' => $roleName === 'pusat' ? $request->pusat_id : null,
        ];
    }
}
