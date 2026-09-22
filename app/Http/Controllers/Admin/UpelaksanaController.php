<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use Illuminate\Http\Request;

class UpelaksanaController extends Controller
{
    public function index()
    {
        $unit_kerjas = UnitKerja::query()
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('admin.layout.unit', compact('unit_kerjas'));
    }

    public function create()
    {
        return redirect()->route('upelaksana.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit_pelaksana' => 'required|string|max:255|unique:unit_kerjas,nama_unit_pelaksana',
        ]);

        UnitKerja::create($request->all());

        return redirect()
            ->route('upelaksana.index')
            ->with('success', 'Unit Pelaksana berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return redirect()->route('upelaksana.index');
    }

    public function update(Request $request, $id)
    {
        $upelaksana = UnitKerja::findOrFail($id);
        $request->validate([
            'nama_unit_pelaksana' => 'required|string|max:255|unique:unit_kerjas,nama_unit_pelaksana,' . $upelaksana->id,
        ]);

        $upelaksana->update($request->all());

        return redirect()
            ->route('upelaksana.index')
            ->with('success', 'Unit Pelaksana berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $upelaksana = UnitKerja::findOrFail($id);
        $upelaksana->delete();

        return redirect()
            ->route('upelaksana.index')
            ->with('success', 'Unit Pelaksana berhasil dihapus.');
    }
}
