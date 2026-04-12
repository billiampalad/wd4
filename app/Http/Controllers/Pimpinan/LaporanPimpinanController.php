<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\KegiatanKerjasama;
use App\Models\JenisKerjasama;
use App\Models\Jurusan;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GlobalLaporanExport;
use Illuminate\Support\Facades\DB;

class LaporanPimpinanController extends Controller
{
    public function index()
    {
        $jenisKerjasama = JenisKerjasama::all();
        $jurusans = Jurusan::all();
        $units = UnitKerja::all();

        return view('auth.pimpinan', [
            'view' => 'laporan',
            'jenisKerjasama' => $jenisKerjasama,
            'jurusans' => $jurusans,
            'units' => $units
        ]);
    }

    private function getFilteredData(Request $request)
    {
        $query = KegiatanKerjasama::with(['jurusans', 'unitKerjas', 'mitras', 'jenisKerjasama']);

        // Filter Rentang Waktu
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('periode_mulai', '>=', $request->tanggal_awal);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('periode_selesai', '<=', $request->tanggal_akhir);
        }

        // Filter Jenis Kerjasama
        if ($request->filled('id_jenis') && $request->id_jenis != 'all') {
            $query->whereHas('jenisKerjasama', fn($q) => $q->where('jenis_kerjasamas.id', $request->id_jenis));
        }

        // Filter Status (Wajib Selesai/Layak jika diminta)
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter Kategori Mitra (Nasional/Internasional)
        if ($request->filled('kategori_mitra') && $request->kategori_mitra != 'all') {
            $query->whereHas('mitras', fn($q) => $q->where('kategori', $request->kategori_mitra));
        }

        // Filter Pengusul / Pelaksana
        if ($request->filled('pengusul') && $request->pengusul != 'all') {
            if (str_starts_with($request->pengusul, 'jurusan_')) {
                $jurusanId = str_replace('jurusan_', '', $request->pengusul);
                $query->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $jurusanId));
            } elseif (str_starts_with($request->pengusul, 'unit_')) {
                $unitId = str_replace('unit_', '', $request->pengusul);
                $query->whereHas('unitKerjas', fn($q) => $q->where('unit_kerjas.id', $unitId));
            }
        }

        return $query->latest()->get();
    }

    public function preview(Request $request)
    {
        // Re-ensure relations are loaded correctly
        $data = $this->getFilteredData($request);
        
        $results = $data->map(function($item) {
            // Label Pengusul - Cek Jurusan & Unit Kerja
            $pengusulArr = [];
            
            if ($item->jurusans && $item->jurusans->count() > 0) {
                foreach($item->jurusans as $j) {
                    $pengusulArr[] = $j->nama_jurusan;
                }
            } 
            
            if ($item->unitKerjas && $item->unitKerjas->count() > 0) {
                foreach($item->unitKerjas as $u) {
                    $pengusulArr[] = $u->nama_unit_pelaksana;
                }
            }
            
            $pengusulLabel = !empty($pengusulArr) ? implode(', ', $pengusulArr) : 'N/A';
            
            // Label Mitra & Kategori
            $mitraInfo = $item->mitras->map(function($m) {
                return $m->nama_mitra . ' (' . ucfirst($m->kategori) . ')';
            })->implode(', ');
            
            // Label Periode
            $mulai = $item->periode_mulai ? $item->periode_mulai->format('d/m/Y') : '-';
            $selesai = $item->periode_selesai ? $item->periode_selesai->format('d/m/Y') : 'Selesai';
            
            // Convert to array and manually add the labels to ensure they are in the JSON
            $arr = $item->toArray();
            $arr['pengusul_label'] = $pengusulLabel;
            $arr['mitra_info'] = $mitraInfo ?: 'N/A';
            $arr['jenis_kerjasama_label'] = $item->jenisKerjasama->pluck('nama_kerjasama')->implode(', ') ?: '-';
            $arr['periode_label'] = "$mulai - $selesai";
            
            // Ensure status attributes from model appends are included
            $arr['status_label'] = $item->status_label;
            $arr['status_class'] = $item->status_class;
            
            return $arr;
        });

        return response()->json($results);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);
        $pdf = Pdf::loadView('auth.layout.pimpinan.laporan_pdf', [
            'data' => $data,
            'request' => $request
        ])->setPaper('a4', 'landscape');
        
        $filename = 'Laporan_Global_Kerjasama_Pimpinan.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getFilteredData($request);
        return Excel::download(new GlobalLaporanExport($data), 'Laporan_Global_Kerjasama_Pimpinan.xlsx');
        // return Excel::download(new GlobalLaporanExport($data), 'Laporan_Global_Kerjasama_' . date('Ymd_His') . '.xlsx');
    }
}
