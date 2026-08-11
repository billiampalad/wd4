<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klasifikasi;
use App\Models\Mitra;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = Mitra::with('cooperations')->latest()->get();
        return view('admin.mitra.index', compact('mitras'));
    }

    public function create()
    {
        $klasifikasis = Klasifikasi::orderBy('nama', 'asc')->get();

        return view('admin.mitra.create', compact('klasifikasis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'id_klasifikasi' => 'nullable|exists:klasifikasi,id',
            'kategori' => 'required|in:nasional,internasional',
            'negara' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telp' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
        ]);

        Mitra::create($request->only([
            'nama_mitra',
            'id_klasifikasi',
            'kategori',
            'negara',
            'alamat',
            'telp',
            'website',
        ]));

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function edit(Mitra $mitra)
    {
        $klasifikasis = Klasifikasi::orderBy('nama', 'asc')->get();

        return view('admin.mitra.edit', compact('mitra', 'klasifikasis'));
    }

    public function update(Request $request, Mitra $mitra)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'id_klasifikasi' => 'nullable|exists:klasifikasi,id',
            'kategori' => 'required|in:nasional,internasional',
            'negara' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telp' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
        ]);

        $mitra->update($request->only([
            'nama_mitra',
            'id_klasifikasi',
            'kategori',
            'negara',
            'alamat',
            'telp',
            'website',
        ]));

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy(Mitra $mitra)
    {
        $mitra->delete();
        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }

    public function sendAccessLogin(Mitra $mitra)
    {
        if (!$mitra->email) {
            return back()->with('error', 'Mitra tidak memiliki email.');
        }

        $password = \Illuminate\Support\Str::random(10);
        $user = \App\Models\User::create([
            'nik' => 'MITRA' . $mitra->id,
            'name' => $mitra->nama_mitra,
            'email' => $mitra->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role_id' => \App\Models\Role::where('role_name', 'mitra')->first()?->id,
            'mitra_id' => $mitra->id,
        ]);

        return back()->with('success', 'Akses login berhasil dikirim ke ' . $user->email . '. Password: ' . $password);
    }
}
