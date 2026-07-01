<?php

namespace App\Http\Controllers;

use App\Models\Klasifikasi;
use App\Models\JenisKerjasama;
use App\Models\Mitra;
use App\Models\Notifikasi;
use App\Models\PengajuanKerjasamaMitra;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PublicPengajuanKerjasamaController extends Controller
{
    public function create()
    {
        $klasifikasis = Klasifikasi::orderBy('nama')->get();
        $jenisKerjasamas = JenisKerjasama::orderBy('nama_kerjasama')->get();

        return view('auth.pengajuan-mitra', compact('klasifikasis', 'jenisKerjasamas'));
    }

    public function store(Request $request)
    {
        $website = trim((string) $request->input('website', ''));
        if ($website !== '' && ! preg_match('/^https?:\/\//i', $website)) {
            $request->merge([
                'website' => 'https://' . $website,
            ]);
        }

        $validated = $request->validate([
            'nama_mitra' => ['required', 'string', 'max:255'],
            'id_klasifikasi' => ['required', 'exists:klasifikasi,id'],
            'kategori' => ['required', Rule::in(['nasional', 'internasional'])],
            'negara' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:1000'],
            'telp' => ['required', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'nama_penandatangan' => ['required', 'string', 'max:255'],
            'jabatan_penandatangan' => ['required', 'string', 'max:255'],
            'nama_penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'jabatan_penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telepon' => ['required', 'string', 'max:30'],
            'judul_pengajuan' => ['required', 'string', 'max:255'],
            'tujuan_pengajuan' => ['required', 'string'],
            'ruang_lingkup' => ['required', 'string'],
            'pesan_tambahan' => ['nullable', 'string'],
        ], [
            'website.url' => 'Website harus berupa URL yang valid, misalnya https://contoh.com.',
        ]);

        DB::beginTransaction();

        try {
            $submission = PengajuanKerjasamaMitra::create(array_merge($validated, [
                'kode_pengajuan' => $this->generateSubmissionCode(),
                'status' => PengajuanKerjasamaMitra::STATUS_DIAJUKAN,
                'submitted_at' => now(),
            ]));

            $pimpinans = User::whereHas('role', function ($query) {
                $query->where('role_name', 'pimpinan');
            })->get();

            foreach ($pimpinans as $pimpinan) {
                Notifikasi::send(
                    $pimpinan->id,
                    null,
                    $submission->id,
                    'pengajuan_mitra',
                    'Pengajuan Mitra Baru',
                    "Pengajuan {$submission->kode_pengajuan} dari {$submission->nama_mitra} menunggu validasi Anda.",
                    route('pimpinan.pengajuan_mitra'),
                    'pengajuan_mitra'
                );
            }

            DB::commit();

            return redirect()
                ->route('pengajuan.kerjasama.create')
                ->with('success', "Pengajuan berhasil dikirim dengan kode {$submission->kode_pengajuan}. Tim pimpinan akan meninjau data Anda.");
        } catch (\Exception $exception) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Pengajuan gagal dikirim. ' . $exception->getMessage());
        }
    }

    private function generateSubmissionCode(): string
    {
        $prefix = 'PGM-' . now()->format('Ymd') . '-';
        $sequence = PengajuanKerjasamaMitra::whereDate('created_at', today())->count() + 1;

        do {
            $code = $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            $sequence++;
        } while (PengajuanKerjasamaMitra::where('kode_pengajuan', $code)->exists());

        return $code;
    }

    public function createPerpanjangan()
    {
        $mitras = Mitra::orderBy('nama_mitra')->get();
        $jenisKerjasamas = JenisKerjasama::orderBy('nama_kerjasama')->get();

        return view('auth.perpanjangan', compact('mitras', 'jenisKerjasamas'));
    }

    public function storePerpanjangan(Request $request)
    {
        $validated = $request->validate([
            'mitra_id' => ['required', 'exists:mitra,id'],
            'nama_penandatangan' => ['required', 'string', 'max:255'],
            'jabatan_penandatangan' => ['required', 'string', 'max:255'],
            'nama_penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'jabatan_penanggung_jawab' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telepon' => ['required', 'string', 'max:30'],
            'judul_pengajuan' => ['required', 'string', 'max:255'],
            'tujuan_pengajuan' => ['required', 'string'],
            'ruang_lingkup' => ['required', 'string'],
            'pesan_tambahan' => ['nullable', 'string'],
        ]);

        $mitra = Mitra::findOrFail($validated['mitra_id']);

        DB::beginTransaction();

        try {
            $submission = PengajuanKerjasamaMitra::create(array_merge($validated, [
                'nama_mitra' => $mitra->nama_mitra,
                'id_klasifikasi' => $mitra->id_klasifikasi,
                'kategori' => $mitra->kategori ?: 'nasional',
                'negara' => $mitra->negara,
                'alamat' => $mitra->alamat ?: '-',
                'telp' => $mitra->telp ?: '-',
                'website' => $mitra->website,
                'kode_pengajuan' => $this->generateSubmissionCode(),
                'status' => PengajuanKerjasamaMitra::STATUS_DIAJUKAN,
                'submitted_at' => now(),
            ]));

            $pimpinans = User::whereHas('role', function ($query) {
                $query->where('role_name', 'pimpinan');
            })->get();

            foreach ($pimpinans as $pimpinan) {
                Notifikasi::send(
                    $pimpinan->id,
                    null,
                    $submission->id,
                    'pengajuan_mitra',
                    'Pengajuan Perpanjangan Mitra',
                    "Pengajuan Perpanjangan {$submission->kode_pengajuan} dari {$submission->nama_mitra} menunggu validasi Anda.",
                    route('pimpinan.pengajuan_mitra'),
                    'pengajuan_mitra'
                );
            }

            DB::commit();

            return redirect()
                ->route('pengajuan.perpanjangan.create')
                ->with('success', "Pengajuan perpanjangan berhasil dikirim dengan kode {$submission->kode_pengajuan}. Tim pimpinan akan meninjau data Anda.");
        } catch (\Exception $exception) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Pengajuan perpanjangan gagal dikirim. ' . $exception->getMessage());
        }
    }
}
