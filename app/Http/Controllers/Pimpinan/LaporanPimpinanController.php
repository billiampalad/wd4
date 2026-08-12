<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Jurusan;
use App\Models\Pusat;
use App\Models\Upa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanPimpinanController extends Controller
{
    public function index()
    {
        return view('auth.pimpinan', [
            'view' => 'laporan',
            'jurusans' => Jurusan::orderBy('nama_jurusan')->get(),
            'upas' => Upa::orderBy('nama_upa')->get(),
            'pusats' => Pusat::orderBy('nama_pusat')->get(),
        ]);
    }

    /**
     * Ambil data kerjasama berdasarkan filter dari request.
     * Menggunakan model Cooperation sesuai skema DB saat ini.
     */
    private function getFilteredData(Request $request)
    {
        $query = Cooperation::with(['mitra', 'mitra.klasifikasi', 'jurusan', 'upa', 'pusat'])
            ->latest();

        // Filter tanggal mulai (berdasarkan start_date)
        if ($request->filled('tanggal_awal')) {
            $query->whereDate('start_date', '>=', $request->tanggal_awal);
        }

        // Filter tanggal akhir (berdasarkan end_date)
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('end_date', '<=', $request->tanggal_akhir);
        }

        if ($request->filled('jenis_dokumentasi') && $request->jenis_dokumentasi !== 'all') {
            $jenisDokumentasi = strtolower($request->jenis_dokumentasi);

            $query->where(function ($jenisQuery) use ($jenisDokumentasi) {
                if ($jenisDokumentasi === 'mou') {
                    $jenisQuery->where('jenis', 'like', '%MoU%');
                } elseif ($jenisDokumentasi === 'moa') {
                    $jenisQuery->where('jenis', 'like', '%MoA%');
                } elseif ($jenisDokumentasi === 'ia') {
                    $jenisQuery->where('jenis', 'like', '%IA%')
                        ->where('jenis', 'not like', '%MoA%');
                }
            });
        }

        // Filter tipe pelaksana (jurusan / upa / pusat)
        if ($request->filled('tipe_pelaksana') && $request->tipe_pelaksana !== 'all') {
            if ($request->tipe_pelaksana === 'instansi') {
                $query->where('jenis', 'like', '%MoU%')
                    ->where(function ($typeQuery) {
                        $typeQuery->whereNull('tipe_pelaksana')
                            ->orWhere('tipe_pelaksana', '');
                    })
                    ->whereNull('jurusan_id')
                    ->whereNull('upa_id')
                    ->whereNull('pusat_id');
            } else {
                $query->where('tipe_pelaksana', $request->tipe_pelaksana);
            }
        }

        if ($request->filled('jurusan_id') && $request->jurusan_id !== 'all') {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        if ($request->filled('upa_id') && $request->upa_id !== 'all') {
            $query->where('upa_id', $request->upa_id);
        }

        if ($request->filled('pusat_id') && $request->pusat_id !== 'all') {
            $query->where('pusat_id', $request->pusat_id);
        }

        // Filter status (aktif / proses / kadarluarsa / dst)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_berlaku', $request->status);
        }

        return $query->get();
    }

    /**
     * Preview data via AJAX → JSON.
     * Field yang dikembalikan sesuai buildRow() di laporan.blade.php:
     *   id, title, doc_number, jenis, tipe_pelaksana,
     *   start_date, end_date, status, mitra.{nama_mitra, kategori}
     */
    public function preview(Request $request)
    {
        $data = $this->getFilteredData($request);

        $results = $data->map(function ($item) {
            return [
                'id'             => $item->id,
                'title'          => $item->title,
                'doc_number'     => $item->doc_number,
                'jenis'          => $item->jenis,
                'tipe_pelaksana' => $item->tipe_pelaksana,
                'pelaksana_name' => $item->pelaksana_name,
                'pelaksana_icon' => $item->pelaksana_icon,
                'pelaksana_class' => $item->pelaksana_class,
                'start_date'     => $item->start_date?->toDateString(),
                'end_date'       => $item->end_date?->toDateString(),
                'status'         => $item->status_berlaku,
                'mitra'          => $item->mitra ? [
                    'nama_mitra' => $item->mitra->nama_mitra,
                    'kategori'   => $item->mitra->kategori,
                ] : null,
                'jurusan'        => $item->jurusan ? [
                    'nama_jurusan' => $item->jurusan->nama_jurusan,
                ] : null,
                'upa'            => $item->upa ? [
                    'nama_upa' => $item->upa->nama_upa,
                ] : null,
                'pusat'          => $item->pusat ? [
                    'nama_pusat' => $item->pusat->nama_pusat,
                ] : null,
            ];
        });

        return response()->json($results);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);
        $pdf = Pdf::loadView('auth.layout.pimpinan.laporan_pdf', [
            'data'    => $data,
            'request' => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Kerjasama_Pimpinan.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getFilteredData($request);

        $filename = "Laporan_Kerjasama_Pimpinan_" . date('Ymd') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'ID', 'Nomor Dokumen', 'Judul', 'Jenis', 'Tipe Pelaksana', 
            'Pelaksana', 'Mitra', 'Status', 'Tanggal Mulai', 'Tanggal Berakhir'
        ];

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $item) {
                $row['ID'] = $item->id;
                $row['Nomor Dokumen'] = $item->doc_number;
                $row['Judul'] = $item->title;
                $row['Jenis'] = $item->jenis;
                $row['Tipe Pelaksana'] = $item->tipe_pelaksana;
                $row['Pelaksana'] = $item->pelaksana_name;
                $row['Mitra'] = $item->mitra ? $item->mitra->nama_mitra : '-';
                $row['Status'] = $item->status_berlaku;
                $row['Tanggal Mulai'] = $item->start_date;
                $row['Tanggal Berakhir'] = $item->end_date;

                fputcsv($file, [
                    $row['ID'], 
                    $row['Nomor Dokumen'], 
                    $row['Judul'], 
                    $row['Jenis'], 
                    $row['Tipe Pelaksana'], 
                    $row['Pelaksana'], 
                    $row['Mitra'], 
                    $row['Status'], 
                    $row['Tanggal Mulai'], 
                    $row['Tanggal Berakhir']
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
