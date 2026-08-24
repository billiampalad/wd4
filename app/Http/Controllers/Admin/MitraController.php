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
        $mitras = Mitra::with(['cooperations', 'users'])->latest()->get();
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
            'nama_mitra' => 'required|string|max:255|unique:mitras,nama_mitra',
            'id_klasifikasi' => 'nullable|exists:klasifikasis,id',
            'kategori' => 'required|in:nasional,internasional',
            'negara' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telp' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
        ]);

        Mitra::create([
            'nama_mitra' => $request->nama_mitra,
            'klasifikasi_id' => $request->id_klasifikasi,
            'negara' => $request->negara,
            'alamat' => $request->alamat,
            'telepon' => $request->telp,
            'website' => $request->website,
        ]);

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
            'nama_mitra' => 'required|string|max:255|unique:mitras,nama_mitra,' . $mitra->id,
            'id_klasifikasi' => 'nullable|exists:klasifikasis,id',
            'kategori' => 'required|in:nasional,internasional',
            'negara' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'telp' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
        ]);

        $mitra->update([
            'nama_mitra' => $request->nama_mitra,
            'klasifikasi_id' => $request->id_klasifikasi,
            'negara' => $request->negara,
            'alamat' => $request->alamat,
            'telepon' => $request->telp,
            'website' => $request->website,
        ]);

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy(Mitra $mitra)
    {
        if ($mitra->cooperations()->count() > 0) {
            return redirect()->route('mitra.index')->with('error', 'Mitra tidak dapat dihapus karena masih terkait dengan data kerjasama.');
        }

        $mitra->delete();
        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }

    public function sendAccessLogin(Request $request, Mitra $mitra)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);

        $password = \Illuminate\Support\Str::random(10);
        $user = \App\Models\User::create([
            'nik' => 'MITRA' . $mitra->id,
            'name' => $mitra->nama_mitra,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role_id' => \App\Models\Role::where('role_name', 'mitra')->first()?->id,
            'mitra_id' => $mitra->id,
        ]);

        $mitra->update(['status_akses' => 'Aktif']);

        return back()->with('success', 'Akses login berhasil dikirim ke ' . $user->email . '. Password: ' . $password);
    }
}
