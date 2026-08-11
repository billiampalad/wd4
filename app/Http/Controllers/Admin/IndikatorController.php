<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Indikator;
use App\Models\Sasaran;
use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    public function index()
    {
        $indikators = Indikator::with('sasaran')->latest()->get();
        return view('admin.layout.indikator', compact('indikators'));
    }

    public function create()
    {
        $sasarans = Sasaran::all();
        return view('admin.indikator.create', compact('sasarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sasaran_id' => 'required|exists:sasarans,id',
            'nama_indikator' => 'required|string',
        ]);

        Indikator::create($request->only('sasaran_id', 'nama_indikator'));

        return redirect()->route('indikator.index')->with('success', 'Data Indikator Kinerja berhasil ditambahkan.');
    }

    public function edit(Indikator $indikator)
    {
        $sasarans = Sasaran::all();
        return view('admin.indikator.edit', compact('indikator', 'sasarans'));
    }

    public function update(Request $request, Indikator $indikator)
    {
        $request->validate([
            'sasaran_id' => 'required|exists:sasarans,id',
            'nama_indikator' => 'required|string',
        ]);

        $indikator->update($request->only('sasaran_id', 'nama_indikator'));

        return redirect()->route('indikator.index')->with('success', 'Data Indikator Kinerja berhasil diperbarui.');
    }

    public function destroy(Indikator $indikator)
    {
        $indikator->delete();
        return redirect()->route('indikator.index')->with('success', 'Data Indikator Kinerja berhasil dihapus.');
    }
}
