<?php

namespace App\Http\Controllers\Jurusan;

use App\Http\Controllers\Controller;
use App\Models\KegiatanKerjasama;
use App\Models\JenisKerjasama;
use App\Models\Mitra;
use App\Models\Tujuan;
use App\Models\Pelaksanaan;
use App\Models\Hasil;
use App\Models\Dokumentasi;
use App\Models\Kesimpulan;
use App\Models\PermasalahanSolusi;
use App\Models\Notifikasi;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KerjasamaJurusanController extends Controller
{
    /**
     * Helper: get jurusan ID from the logged-in user's profile.
     */
    private function getJurusanId()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        if (!$profile || !$profile->jurusan_id) {
            abort(403, 'Profil jurusan tidak ditemukan.');
        }
        return $profile->jurusan_id;
    }

    /**
     * Helper: scope query to kegiatan belonging to user's jurusan via pivot.
     */
    private function scopeJurusan($query, $id_jurusan)
    {
        return $query->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan));
    }

    // ─── LIST ────────────────────────────────────────────

    public function index()
    {
        $id_jurusan = $this->getJurusanId();

        $kerjasamaJurusan = KegiatanKerjasama::with(['jenisKerjasama', 'mitras', 'hasils', 'evaluasis'])
            ->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan))
            ->orderBy('created_at', 'asc')
            ->get();

        $notifikasiTerbaru = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('auth.jurusan', compact('notifikasiTerbaru', 'kerjasamaJurusan'));
    }

    // ─── CREATE PAGE ─────────────────────────────────────

    public function create()
    {
        $id_jurusan = $this->getJurusanId();

        $jenisKerjasama = JenisKerjasama::all();
        $mitras = Mitra::orderBy('nama_mitra')->get();

        $notifikasiTerbaru = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('auth.jurusan', compact('notifikasiTerbaru', 'jenisKerjasama', 'mitras'));
    }

    // ─── STORE ───────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'id_jenis' => 'required|array|min:1',
            'id_jenis.*' => 'exists:jenis_kerjasamas,id',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'nomor_mou' => 'nullable|string|max:255',
            'tanggal_mou' => 'nullable|date',
            'penanggung_jawab' => 'nullable|string|max:255',
            'mitra_nama' => 'required|array|min:1',
            'mitra_nama.*' => 'required|string|max:255',
            'mitra_kategori' => 'required|array|min:1',
            'mitra_kategori.*' => 'required|string',
            'dok_link_drive' => 'nullable|string|max:500',
            'dok_keterangan' => 'nullable|string|max:1000',
        ]);

        $id_jurusan = $this->getJurusanId();

        DB::beginTransaction();
        try {
            // 1. Create kegiatan kerjasama
            $kegiatan = KegiatanKerjasama::create([
                'nama_kegiatan' => $request->nama_kegiatan,
                'created_by' => Auth::id(),
                'periode_mulai' => $request->periode_mulai,
                'periode_selesai' => $request->periode_selesai,
                'nomor_mou' => $request->nomor_mou,
                'tanggal_mou' => $request->tanggal_mou,
                'penanggung_jawab' => $request->penanggung_jawab,
                'status' => 'draft',
            ]);

            // 2. Attach jenis kerjasama (many-to-many)
            $kegiatan->jenisKerjasama()->attach($request->id_jenis);

            // 3. Attach jurusan (many-to-many)
            $kegiatan->jurusans()->attach($id_jurusan);

            // 4. Handle & Attach mitras (Manual Input)
            $mitraIds = [];
            foreach ($request->mitra_nama as $index => $nama) {
                $kategori = $request->mitra_kategori[$index] ?? 'nasional';
                $negara = $request->mitra_negara[$index] ?? 'Indonesia';
                if ($kategori === 'nasional') $negara = 'Indonesia';

                $mitra = Mitra::firstOrCreate(
                    ['nama_mitra' => $nama],
                    [
                        'kategori' => $kategori,
                        'negara' => $negara
                    ]
                );
                // Update kategori & negara if existing mitra might have changed
                $mitra->update(['kategori' => $kategori, 'negara' => $negara]);
                $mitraIds[] = $mitra->id;
            }
            $kegiatan->mitras()->attach($mitraIds);

            // 5. Dokumentasi (optional)
            if ($request->filled('dok_link_drive')) {
                Dokumentasi::create([
                    'id_kegiatan' => $kegiatan->id,
                    'link_drive' => $request->dok_link_drive,
                    'keterangan' => $request->dok_keterangan,
                ]);
            }

            DB::commit();
            return redirect()->route('jurusan.dkerjasama')->with('success', 'Data kerjasama berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // ─── EDIT PAGE ───────────────────────────────────────

    public function edit($id)
    {
        $id_jurusan = $this->getJurusanId();

        $kegiatan = KegiatanKerjasama::with(['mitras', 'dokumentasis', 'jenisKerjasama'])
            ->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan))
            ->findOrFail($id);

        $jenisKerjasama = JenisKerjasama::all();
        $mitras = Mitra::orderBy('nama_mitra')->get();

        $notifikasiTerbaru = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('auth.jurusan', compact('notifikasiTerbaru', 'kegiatan', 'jenisKerjasama', 'mitras'));
    }

    // ─── UPDATE ──────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'id_jenis' => 'required|array|min:1',
            'id_jenis.*' => 'exists:jenis_kerjasamas,id',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'nomor_mou' => 'nullable|string|max:255',
            'tanggal_mou' => 'nullable|date',
            'penanggung_jawab' => 'nullable|string|max:255',
            'mitra_nama' => 'required|array|min:1',
            'mitra_nama.*' => 'required|string|max:255',
            'mitra_kategori' => 'required|array|min:1',
            'mitra_kategori.*' => 'required|string',
            'dok_link_drive' => 'nullable|string|max:500',
            'dok_keterangan' => 'nullable|string|max:1000',
        ]);

        $id_jurusan = $this->getJurusanId();

        $kegiatan = KegiatanKerjasama::whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan))
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $kegiatan->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'periode_mulai' => $request->periode_mulai,
                'periode_selesai' => $request->periode_selesai,
                'nomor_mou' => $request->nomor_mou,
                'tanggal_mou' => $request->tanggal_mou,
                'penanggung_jawab' => $request->penanggung_jawab,
            ]);

            // Sync jenis kerjasama (many-to-many)
            $kegiatan->jenisKerjasama()->sync($request->id_jenis);

            // Sync mitras (Manual Input)
            $mitraIds = [];
            foreach ($request->mitra_nama as $index => $nama) {
                $kategori = $request->mitra_kategori[$index] ?? 'nasional';
                $negara = $request->mitra_negara[$index] ?? 'Indonesia';
                if ($kategori === 'nasional') $negara = 'Indonesia';

                $mitra = Mitra::firstOrCreate(
                    ['nama_mitra' => $nama],
                    [
                        'kategori' => $kategori,
                        'negara' => $negara
                    ]
                );
                // Update kategori & negara if existing mitra might have changed
                $mitra->update(['kategori' => $kategori, 'negara' => $negara]);
                $mitraIds[] = $mitra->id;
            }
            $kegiatan->mitras()->sync($mitraIds);

            // Update/create dokumentasi
            if ($request->filled('dok_link_drive')) {
                $dok = $kegiatan->dokumentasis()->first();
                if ($dok) {
                    $dok->update([
                        'link_drive' => $request->dok_link_drive,
                        'keterangan' => $request->dok_keterangan,
                    ]);
                } else {
                    Dokumentasi::create([
                        'id_kegiatan' => $kegiatan->id,
                        'link_drive' => $request->dok_link_drive,
                        'keterangan' => $request->dok_keterangan,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('jurusan.dkerjasama')->with('success', 'Data kerjasama berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    // ─── DESTROY ─────────────────────────────────────────

    public function destroy($id)
    {
        $id_jurusan = $this->getJurusanId();
        $kegiatan = KegiatanKerjasama::whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan))
            ->findOrFail($id);

        $kegiatan->delete();

        return redirect()->route('jurusan.dkerjasama')->with('success', 'Data kerjasama berhasil dihapus.');
    }

    // ─── DETAIL (SHOW) ───────────────────────────────────

    public function show($id)
    {
        $id_jurusan = $this->getJurusanId();

        $kegiatan = KegiatanKerjasama::with([
            'jenisKerjasama',
            'mitras',
            'jurusans',
            'unitKerjas',
            'creator',
            'tujuans',
            'pelaksanaans',
            'hasils',
            'dokumentasis',
            'evaluasis',
            'kesimpulans',
            'permasalahanSolusis',
        ])->whereHas('jurusans', fn($q) => $q->where('jurusans.id', $id_jurusan))
          ->findOrFail($id);

        $notifikasiTerbaru = Notifikasi::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('auth.jurusan', compact('notifikasiTerbaru', 'kegiatan'));
    }

    // ═══════════════════════════════════════════════════════
    // SUB-RESOURCE CRUD (Tujuan, Pelaksanaan, Hasil, Dokumentasi)
    // ═══════════════════════════════════════════════════════

    // ─── TUJUAN ──────────────────────────────────────────

    public function storeTujuan(Request $request, $id)
    {
        $request->validate([
            'tujuan' => 'required|string',
            'sasaran' => 'required|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Tujuan::create([
            'id_kegiatan' => $id,
            'tujuan' => $request->tujuan,
            'sasaran' => $request->sasaran,
        ]);

        return back()->with('success', 'Tujuan berhasil ditambahkan.');
    }

    public function updateTujuan(Request $request, $id, $tujuanId)
    {
        $request->validate([
            'tujuan' => 'required|string',
            'sasaran' => 'required|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        $tujuan = Tujuan::where('id_kegiatan', $id)->findOrFail($tujuanId);
        $tujuan->update($request->only('tujuan', 'sasaran'));

        return back()->with('success', 'Tujuan berhasil diperbarui.');
    }

    public function destroyTujuan($id, $tujuanId)
    {
        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Tujuan::where('id_kegiatan', $id)->findOrFail($tujuanId)->delete();

        return back()->with('success', 'Tujuan berhasil dihapus.');
    }

    // ─── PELAKSANAAN ─────────────────────────────────────

    public function storePelaksanaan(Request $request, $id)
    {
        $request->validate([
            'deskripsi' => 'required|string',
            'cakupan' => 'nullable|string',
            'jumlah_peserta' => 'nullable|integer|min:0',
            'sumber_daya' => 'nullable|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Pelaksanaan::create([
            'id_kegiatan' => $id,
            'deskripsi' => $request->deskripsi,
            'cakupan' => $request->cakupan,
            'jumlah_peserta' => $request->jumlah_peserta,
            'sumber_daya' => $request->sumber_daya,
        ]);

        return back()->with('success', 'Pelaksanaan berhasil ditambahkan.');
    }

    public function updatePelaksanaan(Request $request, $id, $pelaksanaanId)
    {
        $request->validate([
            'deskripsi' => 'required|string',
            'cakupan' => 'nullable|string',
            'jumlah_peserta' => 'nullable|integer|min:0',
            'sumber_daya' => 'nullable|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        $p = Pelaksanaan::where('id_kegiatan', $id)->findOrFail($pelaksanaanId);
        $p->update($request->only('deskripsi', 'cakupan', 'jumlah_peserta', 'sumber_daya'));

        return back()->with('success', 'Pelaksanaan berhasil diperbarui.');
    }

    public function destroyPelaksanaan($id, $pelaksanaanId)
    {
        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Pelaksanaan::where('id_kegiatan', $id)->findOrFail($pelaksanaanId)->delete();

        return back()->with('success', 'Pelaksanaan berhasil dihapus.');
    }

    // ─── HASIL ───────────────────────────────────────────

    public function storeHasil(Request $request, $id)
    {
        $request->validate([
            'hasil_langsung' => 'nullable|string',
            'dampak' => 'nullable|string',
            'manfaat_mahasiswa' => 'nullable|string',
            'manfaat_polimdo' => 'nullable|string',
            'manfaat_mitra' => 'nullable|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Hasil::create(array_merge($request->only('hasil_langsung', 'dampak', 'manfaat_mahasiswa', 'manfaat_polimdo', 'manfaat_mitra'), [
            'id_kegiatan' => $id,
        ]));

        return back()->with('success', 'Hasil & capaian berhasil ditambahkan.');
    }

    public function updateHasil(Request $request, $id, $hasilId)
    {
        $request->validate([
            'hasil_langsung' => 'nullable|string',
            'dampak' => 'nullable|string',
            'manfaat_mahasiswa' => 'nullable|string',
            'manfaat_polimdo' => 'nullable|string',
            'manfaat_mitra' => 'nullable|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        $h = Hasil::where('id_kegiatan', $id)->findOrFail($hasilId);
        $h->update($request->only('hasil_langsung', 'dampak', 'manfaat_mahasiswa', 'manfaat_polimdo', 'manfaat_mitra'));

        return back()->with('success', 'Hasil & capaian berhasil diperbarui.');
    }

    public function destroyHasil($id, $hasilId)
    {
        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Hasil::where('id_kegiatan', $id)->findOrFail($hasilId)->delete();

        return back()->with('success', 'Hasil berhasil dihapus.');
    }

    // ─── DOKUMENTASI (dari detail page) ──────────────────

    public function storeDokumentasi(Request $request, $id)
    {
        $request->validate([
            'link_drive' => 'required|string|max:500',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Dokumentasi::create([
            'id_kegiatan' => $id,
            'link_drive' => $request->link_drive,
            'keterangan' => $request->keterangan,
        ]);

        return back()->with('success', 'Dokumentasi berhasil ditambahkan.');
    }

    public function updateDokumentasi(Request $request, $id, $dokId)
    {
        $request->validate([
            'link_drive' => 'required|string|max:500',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        $dok = Dokumentasi::where('id_kegiatan', $id)->findOrFail($dokId);
        $dok->update($request->only('link_drive', 'keterangan'));

        return back()->with('success', 'Dokumentasi berhasil diperbarui.');
    }

    public function destroyDokumentasi($id, $dokId)
    {
        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        Dokumentasi::where('id_kegiatan', $id)->findOrFail($dokId)->delete();

        return back()->with('success', 'Dokumentasi berhasil dihapus.');
    }

    // ─── PERMASALAHAN & SOLUSI ────────────────────────────

    public function storePermasalahan(Request $request, $id)
    {
        $request->validate([
            'kendala' => 'nullable|string',
            'solusi' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        PermasalahanSolusi::create([
            'id_kegiatan' => $id,
            'kendala' => $request->kendala,
            'solusi' => $request->solusi,
            'rekomendasi' => $request->rekomendasi,
        ]);

        return back()->with('success', 'Permasalahan & solusi berhasil ditambahkan.');
    }

    public function updatePermasalahan(Request $request, $id, $masalahId)
    {
        $request->validate([
            'kendala' => 'nullable|string',
            'solusi' => 'nullable|string',
            'rekomendasi' => 'nullable|string',
        ]);

        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        $masalah = PermasalahanSolusi::where('id_kegiatan', $id)->findOrFail($masalahId);
        $masalah->update($request->only('kendala', 'solusi', 'rekomendasi'));

        return back()->with('success', 'Permasalahan & solusi berhasil diperbarui.');
    }

    public function destroyPermasalahan($id, $masalahId)
    {
        $id_jurusan = $this->getJurusanId();
        $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        PermasalahanSolusi::where('id_kegiatan', $id)->findOrFail($masalahId)->delete();

        return back()->with('success', 'Permasalahan & solusi berhasil dihapus.');
    }

    // ─── SUBMIT TO PIMPINAN ─────────────────────────────

    public function submitToPimpinan($id)
    {
        $id_jurusan = $this->getJurusanId();
        $kegiatan = $this->scopeJurusan(KegiatanKerjasama::query(), $id_jurusan)->findOrFail($id);

        if (! in_array($kegiatan->status, ['draft', 'revisi'], true)) {
            return back()->with('error', 'Pengiriman ke Pimpinan hanya tersedia untuk status Draft atau Perlu Revisi.');
        }

        $kirimUlangSetelahRevisi = $kegiatan->status === 'revisi';

        $kegiatan->loadMissing('jurusans', 'unitKerjas');
        $pengusulLabel = $kegiatan->jurusans->pluck('nama_jurusan')->filter()->join(', ')
            ?: $kegiatan->unitKerjas->pluck('nama_unit_pelaksana')->filter()->join(', ')
            ?: (Auth::user()->profile?->jurusan?->nama_jurusan ?? Auth::user()->name);

        $kegiatan->update(['status' => 'menunggu_evaluasi']);

        // ─── KIRIM NOTIFIKASI KE PIMPINAN ───────────────────────
        $pimpinans = \App\Models\User::whereHas('role', function($q) {
            $q->where('role_name', 'pimpinan');
        })->get();

        $judul = 'Status Evaluasi';
        $pesan = $kirimUlangSetelahRevisi
            ? "Pengusul: {$pengusulLabel}. Kegiatan: {$kegiatan->nama_kegiatan}. Dokumen dikirim ulang setelah revisi — menunggu evaluasi Anda."
            : "Pengusul: {$pengusulLabel}. Kegiatan: {$kegiatan->nama_kegiatan}. Dokumen baru — menunggu evaluasi Anda.";

        foreach ($pimpinans as $pimpinan) {
            Notifikasi::send(
                $pimpinan->id,
                Auth::id(),
                $kegiatan->id,
                $kirimUlangSetelahRevisi ? 'revisi' : 'evaluasi',
                $judul,
                $pesan,
                route('pimpinan.evaluasi')
            );
        }

        return back()->with('success', 'Data kerjasama berhasil dikirim ke Pimpinan untuk dievaluasi.');
    }
}
