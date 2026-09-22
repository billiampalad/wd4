<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::query()
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('admin.layout.jurusan', compact('jurusans'));
    }

    public function create()
    {
        return redirect()->route('jurusan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'nullable|string|max:20|unique:jurusans,kode_jurusan',
            'nama_jurusan' => 'required|string|max:150|unique:jurusans,nama_jurusan',
        ]);

        Jurusan::create($request->only(['kode_jurusan', 'nama_jurusan']));

        return redirect()
            ->route('jurusan.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return redirect()->route('jurusan.index');
    }

    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $request->validate([
            'kode_jurusan' => 'nullable|string|max:20|unique:jurusans,kode_jurusan,' . $jurusan->id,
            'nama_jurusan' => 'required|string|max:150|unique:jurusans,nama_jurusan,' . $jurusan->id,
        ]);

        $jurusan->update($request->only(['kode_jurusan', 'nama_jurusan']));

        return redirect()
            ->route('jurusan.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();

        return redirect()
            ->route('jurusan.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
