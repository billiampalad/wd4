<?php

namespace App\Http\Controllers\Unit;

use App\Http\Controllers\Controller;
use App\Models\Cooperation;
use App\Models\Profile;
use App\Models\Mitra;
use App\Models\Jurusan;
use App\Models\Prodi;
use App\Models\Upa;
use App\Models\Pusat;
use App\Models\JenisKerjasama;
use App\Models\Pejabat;
use App\Models\Sasaran;
use App\Models\DetailKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KerjasamaUnitController extends Controller
{
    /**
     * Helper: get unit_kerja ID from the logged-in user's profile.
     */
    private function getUnitId(): int
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        if (!$profile || !$profile->unit_kerja_id) {
            abort(403, 'Profil unit kerja tidak ditemukan.');
        }
        return (int) $profile->unit_kerja_id;
    }

    // ─── CREATE PAGE ─────────────────────────────────────

    public function create()
    {
        $mitras = Mitra::orderBy('nama_mitra')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $upas = Upa::orderBy('nama_upa')->get();
        $pusats = Pusat::orderBy('nama_pusat')->get();
        $jenisKerjasama = JenisKerjasama::orderBy('nama_kerjasama')->get();
        $sasarans = Sasaran::orderBy('deskripsi')->get();

        return view('auth.unit', compact('mitras', 'jurusans', 'prodis', 'upas', 'pusats', 'jenisKerjasama', 'sasarans'));
    }

    // ─── STORE ───────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'jenis' => 'required|string|in:MoU (Memorandum of Understanding),MoA (Memorandum of Agreement),IA (Implementation Agreement)',
            'doc_number' => 'nullable|string|max:255',
            'pks_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string',
            'document_link' => 'nullable|string|max:255',
            'tipe_pelaksana' => 'nullable|string|in:jurusan,upa,pusat',
            
            // Penggiat validation (minimal)
            'penggiat_mitra_ids' => 'nullable|array',
            'penggiat' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Handle status normalization
            $statusMap = [
                'Aktif' => 'aktif',
                'Dalam Perpanjangan' => 'dalam perpanjangan',
                'Kadarluarsa' => 'kadarluarsa',
                'Tidak Aktif' => 'tidak aktif',
            ];

            $status = $statusMap[$request->status] ?? 'aktif';

            // 1. Handle Internal Pejabats (Pihak 1)
            $penandatanganInternal = null;
            if ($request->nama_penandatangan) {
                $penandatanganInternal = Pejabat::create([
                    'nama' => $request->nama_penandatangan,
                    'jabatan' => $request->jabatan_penandatangan ?? '-',
                ]);
            }

            $pjInternal = null;
            if ($request->nama_penanggung_jawab) {
                $pjInternal = Pejabat::create([
                    'nama' => $request->nama_penanggung_jawab,
                    'jabatan' => $request->jabatan_penanggung_jawab ?? '-',
                ]);
            }

            // 2. Handle Mitra Pejabats (Pihak 2) - Ambil penggiat pertama karena tabel cooperations hanya punya 1 kolom mitra_id
            $mitraId = null;
            $penandatanganMitra = null;
            $pjMitra = null;

            if ($request->penggiat_mitra_ids && count($request->penggiat_mitra_ids) > 0) {
                $mitraId = $request->penggiat_mitra_ids[0] ?: null;
                
                $penggiatData = $request->penggiat[0] ?? null;
                if ($penggiatData) {
                    if (!empty($penggiatData['nama_penandatangan'])) {
                        $penandatanganMitra = Pejabat::create([
                            'nama' => $penggiatData['nama_penandatangan'],
                            'jabatan' => $penggiatData['jabatan_penandatangan'] ?? '-',
                        ]);
                    }
                    if (!empty($penggiatData['nama_pj'])) {
                        $pjMitra = Pejabat::create([
                            'nama' => $penggiatData['nama_pj'],
                            'jabatan' => $penggiatData['jabatan_pj'] ?? '-',
                        ]);
                    }
                }
            }

            // 3. Create Cooperation
            $cooperation = Cooperation::create([
                'title' => $request->title,
                'jenis' => $request->jenis,
                'doc_number' => $request->doc_number,
                'pks_number' => $request->pks_number,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->input_type === 'baru' ? 'Draft' : $status,
                'status_dokumen' => 'Draft',
                'document_link' => $request->document_link,
                'internal_instansi' => $request->nama_instansi ?? 'Politeknik Negeri Manado',
                'mitra_id' => $mitraId,
                'penandatangan_internal_id' => $penandatanganInternal?->id,
                'pj_internal_id' => $pjInternal?->id,
                'penandatangan_mitra_id' => $penandatanganMitra?->id,
                'pj_mitra_id' => $pjMitra?->id,
                'tipe_pelaksana' => $request->tipe_pelaksana,
                'jurusan_id' => ($request->tipe_pelaksana === 'jurusan' && $request->pelaksana_jurusan_ids) ? $request->pelaksana_jurusan_ids[0] : null,
                'upa_id' => ($request->tipe_pelaksana === 'upa' && $request->pelaksana_upa_ids) ? $request->pelaksana_upa_ids[0] : null,
                'pusat_id' => ($request->tipe_pelaksana === 'pusat' && $request->pelaksana_pusat_ids) ? $request->pelaksana_pusat_ids[0] : null,
            ]);

            // 4. Handle Pivot Tables (Jurusan, UPA, Pusat)
            if ($request->tipe_pelaksana === 'jurusan' && $request->pelaksana_jurusan_ids) {
                $cooperation->jurusans()->sync($request->pelaksana_jurusan_ids);
                if ($request->pelaksana_prodi_ids) {
                    $cooperation->prodis()->sync($request->pelaksana_prodi_ids);
                }
            } elseif ($request->tipe_pelaksana === 'upa' && $request->pelaksana_upa_ids) {
                $cooperation->upas()->sync($request->pelaksana_upa_ids);
            } elseif ($request->tipe_pelaksana === 'pusat' && $request->pelaksana_pusat_ids) {
                $cooperation->pusats()->sync($request->pelaksana_pusat_ids);
            }

            // 5. Handle Detail Kegiatans
            if ($request->id_jenis && is_array($request->id_jenis)) {
                foreach ($request->id_jenis as $jenisId) {
                    $detailData = $request->jenis_detail[$jenisId] ?? null;
                    if ($detailData) {
                        // Sanitize nilai_kontrak (remove Rp, spaces, and dots)
                        $cleanNilai = isset($detailData['nilai_kontrak']) ? str_replace(['Rp', '.', ' '], '', $detailData['nilai_kontrak']) : '0';
                        $nilaiKontrak = (float) str_replace(',', '.', $cleanNilai);
                        
                        DetailKegiatan::create([
                            'cooperation_id' => $cooperation->id,
                            'jenis_kerjasama_id' => $jenisId,
                            'sasaran_id' => $detailData['sasaran_id'] ?? null,
                            'nilai_kontrak' => $nilaiKontrak,
                            'income' => $detailData['income'] ?? null,
                            'volume_luaran' => $detailData['volume'] ?? null,
                            'satuan_luaran' => $detailData['satuan_volume'] ?? null,
                            'keterangan' => $detailData['keterangan'] ?? null,
                            'tujuan' => $detailData['tujuan'] ?? null,
                            'indikator_kinerja' => $detailData['indikator_kinerja'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('unit.dkerjasama')->with('success', 'Data kerjasama berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // ─── DETAIL (SHOW) ───────────────────────────────────

    public function show($id)
    {
        $kegiatan = Cooperation::findOrFail($id);
        return view('auth.unit', compact('kegiatan'));
    }

    // ─── EDIT PAGE ───────────────────────────────────────

    public function edit($id)
    {
        $kegiatan = Cooperation::with([
            'mitra', 'penandatanganInternal', 'pjInternal', 
            'penandatanganMitra', 'pjMitra', 
            'jurusans', 'upas', 'pusats', 'prodis', 'details'
        ])->findOrFail($id);
        $mitras = Mitra::orderBy('nama_mitra')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $upas = Upa::orderBy('nama_upa')->get();
        $pusats = Pusat::orderBy('nama_pusat')->get();
        $jenisKerjasama = JenisKerjasama::orderBy('nama_kerjasama')->get();
        $sasarans = Sasaran::orderBy('deskripsi')->get();

        return view('auth.unit', compact('kegiatan', 'mitras', 'jurusans', 'prodis', 'upas', 'pusats', 'jenisKerjasama', 'sasarans'));
    }

    // ─── UPDATE ──────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'jenis' => 'required|string|in:MoU (Memorandum of Understanding),MoA (Memorandum of Agreement),IA (Implementation Agreement)',
            'doc_number' => 'nullable|string|max:255',
            'pks_number' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string',
            'document_link' => 'nullable|string|max:255',
            'tipe_pelaksana' => 'nullable|string|in:jurusan,upa,pusat',
            'penggiat_mitra_ids' => 'nullable|array',
            'penggiat' => 'nullable|array',
        ]);

        $cooperation = Cooperation::findOrFail($id);

        DB::beginTransaction();
        try {
            // Handle status normalization
            $statusMap = [
                'Aktif' => 'aktif',
                'Dalam Perpanjangan' => 'dalam perpanjangan',
                'Kadarluarsa' => 'kadarluarsa',
                'Tidak Aktif' => 'tidak aktif',
            ];

            $status = $statusMap[$request->status] ?? $cooperation->status;

            // 1. Handle Internal Pejabats (Pihak 1)
            if ($request->nama_penandatangan) {
                if ($cooperation->penandatangan_internal_id) {
                    $cooperation->penandatanganInternal->update([
                        'nama' => $request->nama_penandatangan,
                        'jabatan' => $request->jabatan_penandatangan ?? '-',
                    ]);
                } else {
                    $pj = Pejabat::create([
                        'nama' => $request->nama_penandatangan,
                        'jabatan' => $request->jabatan_penandatangan ?? '-',
                    ]);
                    $cooperation->penandatangan_internal_id = $pj->id;
                }
            }

            if ($request->nama_penanggung_jawab) {
                if ($cooperation->pj_internal_id) {
                    $cooperation->pjInternal->update([
                        'nama' => $request->nama_penanggung_jawab,
                        'jabatan' => $request->jabatan_penanggung_jawab ?? '-',
                    ]);
                } else {
                    $pj = Pejabat::create([
                        'nama' => $request->nama_penanggung_jawab,
                        'jabatan' => $request->jabatan_penanggung_jawab ?? '-',
                    ]);
                    $cooperation->pj_internal_id = $pj->id;
                }
            }

            // 2. Handle Mitra Pejabats (Pihak 2)
            $mitraId = null;
            if ($request->penggiat_mitra_ids && count($request->penggiat_mitra_ids) > 0) {
                $mitraId = $request->penggiat_mitra_ids[0] ?: null;
                $penggiatData = $request->penggiat[0] ?? null;

                if ($penggiatData) {
                    if (!empty($penggiatData['nama_penandatangan'])) {
                        if ($cooperation->penandatangan_mitra_id) {
                            $cooperation->penandatanganMitra->update([
                                'nama' => $penggiatData['nama_penandatangan'],
                                'jabatan' => $penggiatData['jabatan_penandatangan'] ?? '-',
                            ]);
                        } else {
                            $pj = Pejabat::create([
                                'nama' => $penggiatData['nama_penandatangan'],
                                'jabatan' => $penggiatData['jabatan_penandatangan'] ?? '-',
                            ]);
                            $cooperation->penandatangan_mitra_id = $pj->id;
                        }
                    }
                    if (!empty($penggiatData['nama_pj'])) {
                        if ($cooperation->pj_mitra_id) {
                            $cooperation->pjMitra->update([
                                'nama' => $penggiatData['nama_pj'],
                                'jabatan' => $penggiatData['jabatan_pj'] ?? '-',
                            ]);
                        } else {
                            $pj = Pejabat::create([
                                'nama' => $penggiatData['nama_pj'],
                                'jabatan' => $penggiatData['jabatan_pj'] ?? '-',
                            ]);
                            $cooperation->pj_mitra_id = $pj->id;
                        }
                    }
                }
            }

            // 3. Update Cooperation
            $cooperation->update([
                'title' => $request->title,
                'jenis' => $request->jenis,
                'doc_number' => $request->doc_number,
                'pks_number' => $request->pks_number,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $status,
                'document_link' => $request->document_link,
                'internal_instansi' => $request->nama_instansi ?? 'Politeknik Negeri Manado',
                'mitra_id' => $mitraId,
                'penandatangan_internal_id' => $cooperation->penandatangan_internal_id,
                'pj_internal_id' => $cooperation->pj_internal_id,
                'penandatangan_mitra_id' => $cooperation->penandatangan_mitra_id,
                'pj_mitra_id' => $cooperation->pj_mitra_id,
                'tipe_pelaksana' => $request->tipe_pelaksana,
                'jurusan_id' => ($request->tipe_pelaksana === 'jurusan' && $request->pelaksana_jurusan_ids) ? $request->pelaksana_jurusan_ids[0] : null,
                'upa_id' => ($request->tipe_pelaksana === 'upa' && $request->pelaksana_upa_ids) ? $request->pelaksana_upa_ids[0] : null,
                'pusat_id' => ($request->tipe_pelaksana === 'pusat' && $request->pelaksana_pusat_ids) ? $request->pelaksana_pusat_ids[0] : null,
            ]);

            // 4. Handle Pivot Tables (Jurusan, UPA, Pusat)
            // Reset dulu agar tidak dobel
            $cooperation->jurusans()->detach();
            $cooperation->upas()->detach();
            $cooperation->pusats()->detach();
            $cooperation->prodis()->detach();

            if ($request->tipe_pelaksana === 'jurusan' && $request->pelaksana_jurusan_ids) {
                $cooperation->jurusans()->sync($request->pelaksana_jurusan_ids);
                if ($request->pelaksana_prodi_ids) {
                    $cooperation->prodis()->sync($request->pelaksana_prodi_ids);
                }
            } elseif ($request->tipe_pelaksana === 'upa' && $request->pelaksana_upa_ids) {
                $cooperation->upas()->sync($request->pelaksana_upa_ids);
            } elseif ($request->tipe_pelaksana === 'pusat' && $request->pelaksana_pusat_ids) {
                $cooperation->pusats()->sync($request->pelaksana_pusat_ids);
            }

            // 5. Handle Detail Kegiatans
            $cooperation->details()->delete();
            if ($request->id_jenis && is_array($request->id_jenis)) {
                foreach ($request->id_jenis as $jenisId) {
                    $detailData = $request->jenis_detail[$jenisId] ?? null;
                    if ($detailData) {
                    // Sanitize nilai_kontrak (remove Rp, spaces, and dots)
                    $cleanNilai = isset($detailData['nilai_kontrak']) ? str_replace(['Rp', '.', ' '], '', $detailData['nilai_kontrak']) : '0';
                    $nilaiKontrak = (float) str_replace(',', '.', $cleanNilai);

                    DetailKegiatan::create([
                            'cooperation_id' => $cooperation->id,
                            'jenis_kerjasama_id' => $jenisId,
                            'sasaran_id' => $detailData['sasaran_id'] ?? null,
                            'nilai_kontrak' => $nilaiKontrak,
                            'income' => $detailData['income'] ?? null,
                            'volume_luaran' => $detailData['volume'] ?? null,
                            'satuan_luaran' => $detailData['satuan_volume'] ?? null,
                            'keterangan' => $detailData['keterangan'] ?? null,
                            'tujuan' => $detailData['tujuan'] ?? null,
                            'indikator_kinerja' => $detailData['indikator_kinerja'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('unit.dkerjasama')->with('success', 'Data kerjasama berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    // ─── DESTROY ─────────────────────────────────────────

    public function destroy($id)
    {
        $kegiatan = Cooperation::findOrFail($id);
        $kegiatan->delete();

        return redirect()->route('unit.dkerjasama')->with('success', 'Data kerjasama berhasil dihapus.');
    }
}