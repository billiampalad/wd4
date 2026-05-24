<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Models\Jurusan;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['role', 'profile.jurusan', 'profile.unitKerja'])->latest()->get();
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
        return view('admin.users.create', compact('roles', 'jurusans', 'unitKerjas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $profileData = $this->profileDataForRole($request);

        $user = User::create([
            'nik' => $request->nik,
            'name' => $request->name,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);

        $user->profile()->create($profileData);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('profile')->findOrFail($id);
        $roles = Role::all();
        $jurusans = Jurusan::all();
        $unitKerjas = UnitKerja::all();

        return view('admin.users.edit', compact('user', 'roles', 'jurusans', 'unitKerjas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $profileData = $this->profileDataForRole($request);

        $userData = $request->only('name', 'nik', 'role_id');
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
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
        ];
    }
}
