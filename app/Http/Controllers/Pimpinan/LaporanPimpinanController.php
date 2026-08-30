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
        $query = Cooperation::with([
            'mitra',
            'mitra.klasifikasi',
            'jurusan',
            'upa',
            'pusat',
            'jurusans',
            'upas',
            'pusats',
            'pksNumbers',
            'details',
            'details.sasaran',
            'details.indikator',
            'evaluasis',
            'pjInternal',
        ])->latest();

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

        // Filter tipe pelaksana (jurusan / upa / pusat / instansi)
        if ($request->filled('tipe_pelaksana') && $request->tipe_pelaksana !== 'all') {
            $tp = strtolower($request->tipe_pelaksana);
            if ($tp === 'jurusan') {
                $query->where(function ($q) {
                    $q->whereHas('jurusans')->orWhereNotNull('jurusan_id');
                });
            } elseif ($tp === 'upa') {
                $query->where(function ($q) {
                    $q->whereHas('upas')->orWhereNotNull('upa_id');
                });
            } elseif ($tp === 'pusat') {
                $query->where(function ($q) {
                    $q->whereHas('pusats')->orWhereNotNull('pusat_id');
                });
            } elseif ($tp === 'instansi') {
                $query->whereDoesntHave('jurusans')
                    ->whereNull('jurusan_id')
                    ->whereDoesntHave('upas')
                    ->whereNull('upa_id')
                    ->whereDoesntHave('pusats')
                    ->whereNull('pusat_id');
            }
        }

        if ($request->filled('jurusan_id') && $request->jurusan_id !== 'all') {
            $jId = $request->jurusan_id;
            $query->where(function ($q) use ($jId) {
                $q->where('jurusan_id', $jId)
                  ->orWhereHas('jurusans', fn($sq) => $sq->where('jurusans.id', $jId));
            });
        }

        if ($request->filled('upa_id') && $request->upa_id !== 'all') {
            $uId = $request->upa_id;
            $query->where(function ($q) use ($uId) {
                $q->where('upa_id', $uId)
                  ->orWhereHas('upas', fn($sq) => $sq->where('upas.id', $uId));
            });
        }

        if ($request->filled('pusat_id') && $request->pusat_id !== 'all') {
            $pId = $request->pusat_id;
            $query->where(function ($q) use ($pId) {
                $q->where('pusat_id', $pId)
                  ->orWhereHas('pusats', fn($sq) => $sq->where('pusats.id', $pId));
            });
        }

        // Filter status (aktif / proses / kadarluarsa / dst)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status_berlaku', $request->status);
        }

        return $query->get();
    }

    /**
     * Preview data via AJAX → JSON.
     */
    public function preview(Request $request)
    {
        $data = $this->getFilteredData($request);

        $results = $data->map(function ($item) {
            $title = $item->judul ?: ($item->title ?: 'Dokumen Kerjasama');
            $docNumber = $item->doc_number ?: ($item->nomor_dokumen ?: ($item->pksNumbers->first()?->nomor_pks ?? '-'));

            $tipePelaksana = 'instansi';
            $pelaksanaName = $item->internal_instansi ?: 'Politeknik Negeri Manado';
            $pelaksanaIcon = 'fa-building';
            $pelaksanaClass = 'dk-entity-indigo';

            if ($item->jurusans->isNotEmpty() || $item->jurusan) {
                $tipePelaksana = 'jurusan';
                $pelaksanaName = $item->jurusans->pluck('nama_jurusan')->filter()->implode(', ') ?: ($item->jurusan?->nama_jurusan ?? 'Jurusan');
                $pelaksanaIcon = 'fa-microchip';
                $pelaksanaClass = 'dk-entity-blue';
            } elseif ($item->upas->isNotEmpty() || $item->upa) {
                $tipePelaksana = 'upa';
                $pelaksanaName = $item->upas->pluck('nama_upa')->filter()->implode(', ') ?: ($item->upa?->nama_upa ?? 'UPA');
                $pelaksanaIcon = 'fa-building-columns';
                $pelaksanaClass = 'dk-entity-cyan';
            } elseif ($item->pusats->isNotEmpty() || $item->pusat) {
                $tipePelaksana = 'pusat';
                $pelaksanaName = $item->pusats->pluck('nama_pusat')->filter()->implode(', ') ?: ($item->pusat?->nama_pusat ?? 'Pusat');
                $pelaksanaIcon = 'fa-landmark';
                $pelaksanaClass = 'dk-entity-violet';
            }

            return [
                'id'              => $item->id,
                'title'           => $title,
                'judul'           => $title,
                'doc_number'      => $docNumber,
                'jenis'           => $item->jenis,
                'tipe_pelaksana'  => $tipePelaksana,
                'pelaksana_name'  => $pelaksanaName,
                'pelaksana_icon'  => $pelaksanaIcon,
                'pelaksana_class' => $pelaksanaClass,
                'start_date'      => $item->start_date?->toDateString(),
                'end_date'        => $item->end_date?->toDateString(),
                'status'          => $item->status_berlaku,
                'mitra'           => $item->mitra ? [
                    'nama_mitra' => $item->mitra->nama_mitra,
                    'kategori'   => $item->mitra->kategori,
                ] : null,
                'jurusan'         => $item->jurusan ? [
                    'nama_jurusan' => $item->jurusan->nama_jurusan,
                ] : null,
                'upa'             => $item->upa ? [
                    'nama_upa' => $item->upa->nama_upa,
                ] : null,
                'pusat'           => $item->pusat ? [
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

        return $pdf->download('Laporan_Kerjasama_Pimpinan_' . date('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getFilteredData($request);

        $filename = "Laporan_Kerjasama_Pimpinan_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'No', 'Nomor Dokumen', 'Judul Kerjasama', 'Jenis', 'Tipe Pelaksana', 
            'Unit Pelaksana', 'Mitra Kerjasama', 'Status Berlaku', 'Tanggal Mulai', 'Tanggal Berakhir'
        ];

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns);

            foreach ($data as $index => $item) {
                $title = $item->judul ?: ($item->title ?: 'Dokumen Kerjasama');
                $docNumber = $item->doc_number ?: ($item->nomor_dokumen ?: ($item->pksNumbers->first()?->nomor_pks ?? '-'));

                $tipePelaksana = 'Instansi';
                $pelaksanaName = $item->internal_instansi ?: 'Politeknik Negeri Manado';

                if ($item->jurusans->isNotEmpty() || $item->jurusan) {
                    $tipePelaksana = 'Jurusan';
                    $pelaksanaName = $item->jurusans->pluck('nama_jurusan')->filter()->implode(', ') ?: ($item->jurusan?->nama_jurusan ?? 'Jurusan');
                } elseif ($item->upas->isNotEmpty() || $item->upa) {
                    $tipePelaksana = 'UPA';
                    $pelaksanaName = $item->upas->pluck('nama_upa')->filter()->implode(', ') ?: ($item->upa?->nama_upa ?? 'UPA');
                } elseif ($item->pusats->isNotEmpty() || $item->pusat) {
                    $tipePelaksana = 'Pusat';
                    $pelaksanaName = $item->pusats->pluck('nama_pusat')->filter()->implode(', ') ?: ($item->pusat?->nama_pusat ?? 'Pusat');
                }

                fputcsv($file, [
                    $index + 1,
                    $docNumber,
                    $title,
                    $item->jenis ?? '-',
                    $tipePelaksana,
                    $pelaksanaName,
                    $item->mitra ? $item->mitra->nama_mitra : '-',
                    $item->status_berlaku ?? '-',
                    $item->start_date ? $item->start_date->format('d/m/Y') : '-',
                    $item->end_date ? $item->end_date->format('d/m/Y') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
