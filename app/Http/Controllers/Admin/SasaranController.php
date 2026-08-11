<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sasaran;
use Illuminate\Http\Request;

class SasaranController extends Controller
{
    public function index()
    {
        $sasarans = Sasaran::withCount('indikators')->latest()->get();
        return view('admin.layout.sasaran', compact('sasarans'));
    }

    public function create()
    {
        return view('admin.sasaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required|string',
        ]);

        Sasaran::create($request->only('deskripsi'));

        return redirect()->route('sasaran.index')->with('success', 'Data Sasaran IKU berhasil ditambahkan.');
    }

    public function edit(Sasaran $sasaran)
    {
        return view('admin.sasaran.edit', compact('sasaran'));
    }

    public function update(Request $request, Sasaran $sasaran)
    {
        $request->validate([
            'deskripsi' => 'required|string',
        ]);

        $sasaran->update($request->only('deskripsi'));

        return redirect()->route('sasaran.index')->with('success', 'Data Sasaran IKU berhasil diperbarui.');
    }

    public function destroy(Sasaran $sasaran)
    {
        $sasaran->delete();
        return redirect()->route('sasaran.index')->with('success', 'Data Sasaran IKU berhasil dihapus.');
    }
}
