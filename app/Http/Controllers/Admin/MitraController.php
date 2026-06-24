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
}
