<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class ProdiController extends Controller
{
    public function index()
    {
        $prodis = Prodi::with('jurusan')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('admin.layout.prodi', compact('prodis'));
    }

    public function create()
    {
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        return view('admin.prodi.create', compact('jurusans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id'  => 'required|exists:jurusans,id',
            'kode_prodi'  => 'nullable|string|max:20|unique:prodis,kode_prodi',
            'nama_prodi'  => 'required|string|max:150',
            'jenjang'     => 'required|in:D3,D4,S1,S2',
        ]);

        Prodi::create($request->only(['jurusan_id', 'kode_prodi', 'nama_prodi', 'jenjang']));

        return redirect()
            ->route('prodi.index')
            ->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $prodi = Prodi::findOrFail($id);
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        return view('admin.prodi.edit', compact('prodi', 'jurusans'));
    }

    public function update(Request $request, $id)
    {
        $prodi = Prodi::findOrFail($id);
        $request->validate([
            'jurusan_id'  => 'required|exists:jurusans,id',
            'kode_prodi'  => 'nullable|string|max:20|unique:prodis,kode_prodi,' . $prodi->id,
            'nama_prodi'  => 'required|string|max:150',
            'jenjang'     => 'required|in:D3,D4,S1,S2',
        ]);

        $prodi->update($request->only(['jurusan_id', 'kode_prodi', 'nama_prodi', 'jenjang']));

        return redirect()
            ->route('prodi.index')
            ->with('success', 'Program Studi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $prodi = Prodi::findOrFail($id);
        $prodi->delete();

        return redirect()
            ->route('prodi.index')
            ->with('success', 'Program Studi berhasil dihapus.');
    }
}
