<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mitra;
use Illuminate\Http\Request;

class MitraController extends Controller
{
    public function index()
    {
        $mitras = Mitra::with('kegiatanKerjasamas')->latest()->get();
        return view('admin.mitra.index', compact('mitras'));
    }

    public function create()
    {
        return view('admin.mitra.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'negara' => 'nullable|string|max:255',
            'kategori' => 'required|in:nasional,internasional',
        ]);

        Mitra::create($request->all());

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    public function edit(Mitra $mitra)
    {
        return view('admin.mitra.edit', compact('mitra'));
    }

    public function update(Request $request, Mitra $mitra)
    {
        $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'negara' => 'nullable|string|max:255',
            'kategori' => 'required|in:nasional,internasional',
        ]);

        $mitra->update($request->all());

        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil diperbarui.');
    }

    public function destroy(Mitra $mitra)
    {
        $mitra->delete();
        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }
}
