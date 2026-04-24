<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klasifikasi;
use Illuminate\Http\Request;

class KlasifikasiController extends Controller
{
    public function index()
    {
        $klasifikasi = Klasifikasi::all();
        return view('admin.klasifikasi.index', compact('klasifikasi'));
    }

    public function create()
    {
        return view('admin.klasifikasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        Klasifikasi::create($request->all());

        return redirect()->route('klasifikasi.index')->with('success', 'Klasifikasi berhasil ditambahkan.');
    }

    public function edit(Klasifikasi $klasifikasi)
    {
        return view('admin.klasifikasi.edit', compact('klasifikasi'));
    }

    public function update(Request $request, Klasifikasi $klasifikasi)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
        ]);

        $klasifikasi->update($request->all());

        return redirect()->route('klasifikasi.index')->with('success', 'Klasifikasi berhasil diperbarui.');
    }

    public function destroy(Klasifikasi $klasifikasi)
    {
        $klasifikasi->delete();

        return redirect()->route('klasifikasi.index')->with('success', 'Klasifikasi berhasil dihapus.');
    }
}
