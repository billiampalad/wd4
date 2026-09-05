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

use App\Models\Cooperation;
use App\Models\PengajuanKerjasamaBaru;
use App\Models\PengajuanPerpanjanganKerjasama;
use App\Models\KegiatanKerjasama;
use App\Models\Notifikasi;

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
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::create([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_verified_at' => now(),
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
        ]);

        $profileData = $this->profileDataForRole($request);

        $user->profile()->create($profileData);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['role', 'profile.jurusan', 'profile.unitKerja', 'profile.upa', 'profile.pusat', 'mitra.klasifikasi'])->findOrFail($id);
        
        $roleKey = strtolower($user->role?->role_name ?? '');

        // Query related cooperations & activities based on user context
        if (($roleKey === 'mitra' || $user->mitra_id) && $user->mitra_id) {
            $cooperations = Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat'])
                ->where('mitra_id', $user->mitra_id)
                ->latest()
                ->get();
            $proposals = PengajuanKerjasamaBaru::where('mitra_id', $user->mitra_id)
                ->orWhere('email', $user->email)
                ->latest()
                ->get();
            $perpanjangans = PengajuanPerpanjanganKerjasama::where('mitra_id', $user->mitra_id)
                ->orWhere('email', $user->email)
                ->latest()
                ->get();
            $kegiatanKerjasamas = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.jenisKerjasama', 'evaluasis'])
                ->whereHas('cooperation', fn($q) => $q->where('mitra_id', $user->mitra_id))
                ->latest()
                ->take(20)
                ->get();
        } elseif ($roleKey === 'jurusan' && $user->profile?->jurusan_id) {
            $jurusanId = $user->profile->jurusan_id;
            $cooperations = Cooperation::with(['mitra', 'jurusan'])
                ->where('jurusan_id', $jurusanId)
                ->orWhereHas('jurusans', fn($q) => $q->where('jurusans.id', $jurusanId))
                ->latest()
                ->get();
            $proposals = collect();
            $perpanjangans = collect();
            $kegiatanKerjasamas = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.jenisKerjasama', 'evaluasis'])
                ->whereHas('cooperation', function ($q) use ($jurusanId) {
                    $q->where('jurusan_id', $jurusanId)
                      ->orWhereHas('jurusans', fn($sq) => $sq->where('jurusans.id', $jurusanId));
                })
                ->latest()
                ->take(20)
                ->get();
        } elseif ($roleKey === 'upa' && $user->profile?->upa_id) {
            $upaId = $user->profile->upa_id;
            $cooperations = Cooperation::with(['mitra', 'upa'])
                ->where('upa_id', $upaId)
                ->latest()
                ->get();
            $proposals = collect();
            $perpanjangans = collect();
            $kegiatanKerjasamas = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.jenisKerjasama', 'evaluasis'])
                ->whereHas('cooperation', fn($q) => $q->where('upa_id', $upaId))
                ->latest()
                ->take(20)
                ->get();
        } elseif ($roleKey === 'pusat' && $user->profile?->pusat_id) {
            $pusatId = $user->profile->pusat_id;
            $cooperations = Cooperation::with(['mitra', 'pusat'])
                ->where('pusat_id', $pusatId)
                ->latest()
                ->get();
            $proposals = collect();
            $perpanjangans = collect();
            $kegiatanKerjasamas = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.jenisKerjasama', 'evaluasis'])
                ->whereHas('cooperation', fn($q) => $q->where('pusat_id', $pusatId))
                ->latest()
                ->take(20)
                ->get();
        } elseif ($roleKey === 'unit_kerja' || $roleKey === 'humas') {
            $cooperations = Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat'])
                ->where('created_by', $user->id)
                ->orWhere('updated_by', $user->id)
                ->latest()
                ->take(20)
                ->get();
            $proposals = PengajuanKerjasamaBaru::latest()->take(10)->get();
            $perpanjangans = PengajuanPerpanjanganKerjasama::latest()->take(10)->get();
            $kegiatanKerjasamas = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.jenisKerjasama', 'evaluasis'])
                ->latest()
                ->take(20)
                ->get();
        } else {
            // Admin & Pimpinan: overview summary
            $cooperations = Cooperation::with(['mitra', 'jurusan', 'upa', 'pusat'])
                ->latest()
                ->take(15)
                ->get();
            $proposals = PengajuanKerjasamaBaru::latest()->take(10)->get();
            $perpanjangans = PengajuanPerpanjanganKerjasama::latest()->take(10)->get();
            $kegiatanKerjasamas = KegiatanKerjasama::with(['cooperation.mitra', 'detailKegiatan.jenisKerjasama', 'evaluasis'])
                ->latest()
                ->take(20)
                ->get();
        }

        $notifikasis = Notifikasi::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_cooperations'   => $cooperations->count(),
            'active_cooperations'  => $cooperations->where('status_berlaku', 'Aktif')->count(),
            'expiring_cooperations'=> $cooperations->where('status_berlaku', 'Akan Berakhir')->count(),
            'expired_cooperations' => $cooperations->where('status_berlaku', 'Kadaluarsa')->count(),
            'total_kegiatan'       => $kegiatanKerjasamas->count(),
            'total_proposals'      => $proposals->count() + $perpanjangans->count(),
        ];

        return view('admin.users.detail', compact('user', 'cooperations', 'proposals', 'perpanjangans', 'kegiatanKerjasamas', 'notifikasis', 'stats'));
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
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $newEmail = $validated['email'];
        $emailChanged = $newEmail !== $user->email;
        $userData = [
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'email' => $newEmail,
            'role_id' => $validated['role_id'],
        ];
        if ($emailChanged) {
            $userData['email_verified_at'] = now();
        }
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        $profileData = $this->profileDataForRole($request);

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
        $roleName = strtolower(str_replace([' ', '-'], '_', trim((string) $roleName)));
        $roleName = $roleName === 'humas' ? 'unit_kerja' : $roleName;

        return [
            'jabatan' => $request->jabatan,
            'jurusan_id' => $roleName === 'jurusan' ? $request->jurusan_id : null,
            'unit_kerja_id' => $roleName === 'unit_kerja' ? $request->unit_kerja_id : null,
            'upa_id' => $roleName === 'upa' ? $request->upa_id : null,
            'pusat_id' => $roleName === 'pusat' ? $request->pusat_id : null,
        ];
    }
}
