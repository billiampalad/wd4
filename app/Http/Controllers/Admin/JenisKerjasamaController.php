<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisKerjasama;
use Illuminate\Http\Request;

class JenisKerjasamaController extends Controller
{
    public function index()
    {
        // Urutkan dari tertua -> terbaru agar data baru muncul di bagian bawah.
        $jenisKerjasamas = JenisKerjasama::query()
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('admin.layout.jkerjasama', compact('jenisKerjasamas'));
    }

    public function create()
    {
        return view('admin.jkerjasama.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kerjasama' => 'required|string|max:255|unique:jenis_kerjasamas,nama_kerjasama',
        ]);

        JenisKerjasama::create($request->all());

        return redirect()
            ->route('jkerjasama.index')
            ->with('success', 'Jenis Kerjasama berhasil ditambahkan.');
    }

    public function edit(JenisKerjasama $jkerjasama)
    {
        return view('admin.jkerjasama.edit', compact('jkerjasama'));
    }

    public function update(Request $request, JenisKerjasama $jkerjasama)
    {
        $request->validate([
            'nama_kerjasama' => 'required|string|max:255|unique:jenis_kerjasamas,nama_kerjasama,' . $jkerjasama->id,
        ]);

        $jkerjasama->update($request->all());

        return redirect()
            ->route('jkerjasama.index')
            ->with('success', 'Jenis Kerjasama berhasil diperbarui.');
    }

    public function destroy(JenisKerjasama $jkerjasama)
    {
        $jkerjasama->delete();

        return redirect()
            ->route('jkerjasama.index')
            ->with('success', 'Jenis Kerjasama berhasil dihapus.');
    }
}

