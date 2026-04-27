<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Upa;
use Illuminate\Http\Request;

class UpaController extends Controller
{
    public function index()
    {
        $upas = Upa::query()
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('admin.layout.upa', compact('upas'));
    }

    public function create()
    {
        return view('admin.upa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_upa' => 'required|string|max:150|unique:upas,nama_upa',
        ]);

        Upa::create($request->only(['nama_upa']));

        return redirect()
            ->route('upa.index')
            ->with('success', 'UPA berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $upa = Upa::findOrFail($id);
        return view('admin.upa.edit', compact('upa'));
    }

    public function update(Request $request, $id)
    {
        $upa = Upa::findOrFail($id);
        $request->validate([
            'nama_upa' => 'required|string|max:150|unique:upas,nama_upa,' . $upa->id,
        ]);

        $upa->update($request->only(['nama_upa']));

        return redirect()
            ->route('upa.index')
            ->with('success', 'UPA berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $upa = Upa::findOrFail($id);
        $upa->delete();

        return redirect()
            ->route('upa.index')
            ->with('success', 'UPA berhasil dihapus.');
    }
}
