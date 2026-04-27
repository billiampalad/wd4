<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pusat;
use Illuminate\Http\Request;

class PusatController extends Controller
{
    public function index()
    {
        $pusats = Pusat::query()
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('admin.layout.pusat', compact('pusats'));
    }

    public function create()
    {
        return view('admin.pusat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pusat' => 'required|string|max:150|unique:pusats,nama_pusat',
        ]);

        Pusat::create($request->only(['nama_pusat']));

        return redirect()
            ->route('pusat.index')
            ->with('success', 'Pusat berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pusat = Pusat::findOrFail($id);
        return view('admin.pusat.edit', compact('pusat'));
    }

    public function update(Request $request, $id)
    {
        $pusat = Pusat::findOrFail($id);
        $request->validate([
            'nama_pusat' => 'required|string|max:150|unique:pusats,nama_pusat,' . $pusat->id,
        ]);

        $pusat->update($request->only(['nama_pusat']));

        return redirect()
            ->route('pusat.index')
            ->with('success', 'Pusat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pusat = Pusat::findOrFail($id);
        $pusat->delete();

        return redirect()
            ->route('pusat.index')
            ->with('success', 'Pusat berhasil dihapus.');
    }
}
