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
use App\Models\Indikator;
use App\Models\DetailKegiatan;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    public function create(Request $request)
    {
        $mitras = Mitra::orderBy('nama_mitra')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $upas = Upa::orderBy('nama_upa')->get();
        $pusats = Pusat::orderBy('nama_pusat')->get();
        $jenisKerjasama = JenisKerjasama::orderBy('nama_kerjasama')->get();
        $sasarans = Sasaran::orderBy('deskripsi')->get();
        $indikators = Indikator::orderBy('nama_indikator')->get();
        $perpanjanganAsal = null;

        if ($request->filled('perpanjangan_dari')) {
            $perpanjanganAsal = Cooperation::with([
                'mitra',
                'penandatanganInternal',
                'pjInternal',
                'penandatanganMitra',
                'pjMitra',
                'jurusans',
                'prodis',
                'upas',
                'pusats',
                'details.indikator',
                'pksNumbers',
            ])->findOrFail((int) $request->query('perpanjangan_dari'));

            if (!$this->canRequestExtension($perpanjanganAsal)) {
                return redirect()
                    ->route('unit.kerjasama.show', $perpanjanganAsal->id)
                    ->with('error', 'Perpanjangan hanya dapat diajukan untuk dokumen yang sudah disahkan dan masa berlakunya kadaluarsa atau tersisa maksimal 30 hari.');
            }
        }

        return view('auth.unit', compact('mitras', 'jurusans', 'prodis', 'upas', 'pusats', 'jenisKerjasama', 'sasarans', 'indikators', 'perpanjanganAsal'));
    }

    // ─── STORE ───────────────────────────────────────────

    public function store(Request $request)
    {
        $requiresPelaksana = $this->requiresPelaksana($request->input('jenis'));
        $tipePelaksana = $requiresPelaksana
            ? $this->normalizedTipePelaksana($request->input('tipe_pelaksana', []))
            : [];

        $request->merge([
            'pks_numbers' => $this->normalizedPksNumbers($request->input('pks_numbers', []))->all(),
            'tipe_pelaksana' => !empty($tipePelaksana) ? $tipePelaksana : null,
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'jenis' => 'required|string|in:MoU (Memorandum of Understanding),MoA (Memorandum of Agreement),IA (Implementation Agreement)',
            'doc_number' => ['nullable', 'string', 'max:255', Rule::unique('cooperations', 'doc_number')],
            'pks_numbers' => ['nullable', 'array'],
            'pks_numbers.*' => ['nullable', 'string', 'max:255', 'distinct', Rule::unique('pks_numbers', 'number')],
            'description' => 'nullable|string|max:2000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string',
            'document_link' => 'nullable|string|max:255',
            'perpanjangan_dari_id' => 'nullable|exists:cooperations,id',
            'jenis_detail' => 'nullable|array',
            'jenis_detail.*.nilai_kontrak' => 'nullable|string|max:255',
            'jenis_detail.*.income' => 'nullable|string|max:255',
            'jenis_detail.*.volume' => 'nullable|string|max:255',
            'jenis_detail.*.satuan_volume' => 'nullable|string|max:255',
            'jenis_detail.*.keterangan' => 'nullable|string|max:10000',
            'jenis_detail.*.tujuan' => 'nullable|string|max:10000',
            'jenis_detail.*.output' => 'nullable|string|max:10000',
            'jenis_detail.*.outcome' => 'nullable|string|max:10000',
            // Tipe pelaksana hanya wajib jika jenis BUKAN MoU
            'tipe_pelaksana' => [Rule::requiredIf($requiresPelaksana), 'nullable', 'array', 'min:1'],
            'tipe_pelaksana.*' => ['string', Rule::in(['jurusan', 'upa', 'pusat'])],

            // Penggiat validation
            'penggiat_mitra_ids' => 'required|array|min:1',
            'penggiat' => 'required|array|min:1',
        ], [
            'title.required' => 'Judul kerjasama wajib diisi.',
            'jenis.required' => 'Jenis dokumen wajib dipilih.',
            'tipe_pelaksana.required' => 'Tipe pelaksana wajib dipilih untuk dokumen MoA atau IA.',
            'tipe_pelaksana.min' => 'Minimal pilih satu tipe pelaksana.',
            'penggiat_mitra_ids.required' => 'Minimal pilih satu instansi mitra.',
            'jenis_detail.*.volume.max' => 'Volume luaran maksimal 255 karakter.',
            'jenis_detail.*.satuan_volume.max' => 'Satuan luaran maksimal 255 karakter. Isi dengan satuan singkat seperti mahasiswa, orang, sertifikat, dokumen, atau kegiatan.',
            'doc_number.unique' => 'Nomor dokumen sudah digunakan pada data kerjasama lain.',
            'pks_numbers.*.unique' => 'Nomor PKS sudah digunakan pada data kerjasama lain.',
            'pks_numbers.*.distinct' => 'Nomor PKS tidak boleh duplikat dalam satu dokumen.',
        ]);

        $perpanjanganDariId = $request->filled('perpanjangan_dari_id') ? (int) $request->perpanjangan_dari_id : null;
        if ($perpanjanganDariId) {
            $perpanjanganAsal = Cooperation::findOrFail($perpanjanganDariId);

            if (!$this->canRequestExtension($perpanjanganAsal)) {
                return back()
                    ->withInput()
                    ->with('error', 'Perpanjangan hanya dapat diajukan untuk dokumen yang sudah disahkan dan masa berlakunya kadaluarsa atau tersisa maksimal 30 hari.');
            }
        }

        DB::beginTransaction();
        try {
            // Handle status normalization (status masa berlaku)
            $statusMap = [
                'Aktif' => 'aktif',
                'Dalam Perpanjangan' => 'dalam perpanjangan',
                'Kadarluarsa' => 'kadarluarsa',
                'Kadaluarsa' => 'kadarluarsa',
                'Kedaluwarsa' => 'kadarluarsa',
                'Tidak Aktif' => 'tidak aktif',
            ];

            // Perpanjangan memiliki status masa berlaku tersendiri.
            // Jika input baru biasa, status masa berlaku otomatis 'proses'.
            // Jika input arsip, gunakan pilihan user.
            if ($perpanjanganDariId) {
                $status = 'dalam perpanjangan';
            } else {
                $status = ($request->input_type === 'baru') ? 'proses' : ($statusMap[$request->status] ?? 'aktif');
            }

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

            // 2. Handle Mitra Pejabats (Pihak 2)
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
            $primaryTipePelaksana = $tipePelaksana[0] ?? null;
            $cooperation = Cooperation::create([
                'title' => $request->title,
                'jenis' => $request->jenis,
                'doc_number' => $request->doc_number,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $status, // Status Masa Berlaku (aktif, kadarluarsa, dll)
                'status_dokumen' => 'Draft', // Status Alur Dokumen (Draft, Menunggu Evaluasi, Disahkan)
                'perpanjangan_dari_id' => $perpanjanganDariId,
                'document_link' => $request->document_link,
                'internal_instansi' => $request->nama_instansi ?? 'Politeknik Negeri Manado',
                'mitra_id' => $mitraId,
                'penandatangan_internal_id' => $penandatanganInternal?->id,
                'pj_internal_id' => $pjInternal?->id,
                'penandatangan_mitra_id' => $penandatanganMitra?->id,
                'pj_mitra_id' => $pjMitra?->id,
                'tipe_pelaksana' => $primaryTipePelaksana,
                'jurusan_id' => in_array('jurusan', $tipePelaksana, true) && $request->pelaksana_jurusan_ids ? $request->pelaksana_jurusan_ids[0] : null,
                'upa_id' => in_array('upa', $tipePelaksana, true) && $request->pelaksana_upa_ids ? $request->pelaksana_upa_ids[0] : null,
                'pusat_id' => in_array('pusat', $tipePelaksana, true) && $request->pelaksana_pusat_ids ? $request->pelaksana_pusat_ids[0] : null,
            ]);

            $this->syncPksNumbers($cooperation, $request->input('pks_numbers', []));

            // 4. Handle Pivot Tables
            $this->syncPelaksanaRelations($cooperation, $tipePelaksana, $request);

            // 5. Handle Detail Kegiatans (Optional fields)
            if ($request->id_jenis && is_array($request->id_jenis)) {
                foreach ($request->id_jenis as $jenisId) {
                    $detailData = $request->jenis_detail[$jenisId] ?? null;
                    if ($detailData) {
                        // Sanitize nilai_kontrak
                        $cleanNilai = !empty($detailData['nilai_kontrak']) ? str_replace(['Rp', '.', ' '], '', $detailData['nilai_kontrak']) : '0';
                        $nilaiKontrak = (float) str_replace(',', '.', $cleanNilai);

                        DetailKegiatan::create([
                            'cooperation_id' => $cooperation->id,
                            'jenis_kerjasama_id' => $jenisId,
                            'sasaran_id' => $detailData['sasaran_id'] ?: null,
                            'nilai_kontrak' => $nilaiKontrak,
                            'income' => $detailData['income'] ?: null,
                            'volume_luaran' => $detailData['volume'] ?: null,
                            'satuan_luaran' => $detailData['satuan_volume'] ?: null,
                            'keterangan' => $detailData['keterangan'] ?: null,
                            'tujuan' => $detailData['tujuan'] ?: null,
                            'indikator_id' => $detailData['indikator_id'] ?: null,
                            'output' => $detailData['output'] ?: null,
                            'outcome' => $detailData['outcome'] ?: null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('unit.dkerjasama')->with('success', 'Data kerjasama berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $this->formatExceptionMessage($e));
        }
    }

    // ─── DETAIL (SHOW) ───────────────────────────────────

    public function show($id)
    {
        $kegiatan = Cooperation::with([
            'mitra',
            'penandatanganInternal',
            'pjInternal',
            'penandatanganMitra',
            'pjMitra',
            'jurusans',
            'prodis',
            'upas',
            'pusats',
            'details.jenisKerjasama',
            'details.sasaran',
            'details.indikator',
            'evaluasis.penilai',
            'laporanFiles',
            'pksNumbers',
        ])->findOrFail($id);

        return view('auth.unit', compact('kegiatan'));
    }

    // ─── EDIT PAGE ───────────────────────────────────────

    public function edit($id)
    {
        $kegiatan = Cooperation::with([
            'mitra',
            'penandatanganInternal',
            'pjInternal',
            'penandatanganMitra',
            'pjMitra',
            'jurusans',
            'upas',
            'pusats',
            'prodis',
            'details.indikator',
            'pksNumbers',
        ])->findOrFail($id);
        $mitras = Mitra::orderBy('nama_mitra')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $upas = Upa::orderBy('nama_upa')->get();
        $pusats = Pusat::orderBy('nama_pusat')->get();
        $jenisKerjasama = JenisKerjasama::orderBy('nama_kerjasama')->get();
        $sasarans = Sasaran::orderBy('deskripsi')->get();
        $indikators = Indikator::orderBy('nama_indikator')->get();

        return view('auth.unit', compact('kegiatan', 'mitras', 'jurusans', 'prodis', 'upas', 'pusats', 'jenisKerjasama', 'sasarans', 'indikators'));
    }

    // ─── UPDATE ──────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $cooperation = Cooperation::findOrFail($id);
        $requiresPelaksana = $this->requiresPelaksana($request->input('jenis'));
        $tipePelaksana = $requiresPelaksana
            ? $this->normalizedTipePelaksana($request->input('tipe_pelaksana', []))
            : [];

        $request->merge([
            'pks_numbers' => $this->normalizedPksNumbers($request->input('pks_numbers', []))->all(),
            'tipe_pelaksana' => !empty($tipePelaksana) ? $tipePelaksana : null,
        ]);

        $request->validate([
            'title' => 'required|string|max:255',
            'jenis' => 'required|string|in:MoU (Memorandum of Understanding),MoA (Memorandum of Agreement),IA (Implementation Agreement)',
            'doc_number' => ['nullable', 'string', 'max:255', Rule::unique('cooperations', 'doc_number')->ignore($cooperation->id)],
            'pks_numbers' => ['nullable', 'array'],
            'pks_numbers.*' => [
                'nullable',
                'string',
                'max:255',
                'distinct',
                Rule::unique('pks_numbers', 'number')->where(fn ($query) => $query->where('cooperation_id', '<>', $cooperation->id)),
            ],
            'description' => 'nullable|string|max:2000',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status' => 'nullable|string',
            'document_link' => 'nullable|string|max:255',
            'jenis_detail' => 'nullable|array',
            'jenis_detail.*.nilai_kontrak' => 'nullable|string|max:255',
            'jenis_detail.*.income' => 'nullable|string|max:255',
            'jenis_detail.*.volume' => 'nullable|string|max:255',
            'jenis_detail.*.satuan_volume' => 'nullable|string|max:255',
            'jenis_detail.*.keterangan' => 'nullable|string|max:10000',
            'jenis_detail.*.tujuan' => 'nullable|string|max:10000',
            'jenis_detail.*.output' => 'nullable|string|max:10000',
            'jenis_detail.*.outcome' => 'nullable|string|max:10000',
            'tipe_pelaksana' => [Rule::requiredIf($requiresPelaksana), 'nullable', 'array', 'min:1'],
            'tipe_pelaksana.*' => ['string', Rule::in(['jurusan', 'upa', 'pusat'])],
            'penggiat_mitra_ids' => 'required|array|min:1',
            'penggiat' => 'required|array|min:1',
        ], [
            'title.required' => 'Judul kerjasama wajib diisi.',
            'jenis.required' => 'Jenis dokumen wajib dipilih.',
            'tipe_pelaksana.required' => 'Tipe pelaksana wajib dipilih untuk dokumen MoA atau IA.',
            'tipe_pelaksana.min' => 'Minimal pilih satu tipe pelaksana.',
            'penggiat_mitra_ids.required' => 'Minimal pilih satu instansi mitra.',
            'jenis_detail.*.volume.max' => 'Volume luaran maksimal 255 karakter.',
            'jenis_detail.*.satuan_volume.max' => 'Satuan luaran maksimal 255 karakter. Isi dengan satuan singkat seperti mahasiswa, orang, sertifikat, dokumen, atau kegiatan.',
            'doc_number.unique' => 'Nomor dokumen sudah digunakan pada data kerjasama lain.',
            'pks_numbers.*.unique' => 'Nomor PKS sudah digunakan pada data kerjasama lain.',
            'pks_numbers.*.distinct' => 'Nomor PKS tidak boleh duplikat dalam satu dokumen.',
        ]);

        DB::beginTransaction();
        try {
            // Handle status normalization
            $statusMap = [
                'Aktif' => 'aktif',
                'Dalam Perpanjangan' => 'dalam perpanjangan',
                'Kadarluarsa' => 'kadarluarsa',
                'Kadaluarsa' => 'kadarluarsa',
                'Kedaluwarsa' => 'kadarluarsa',
                'Tidak Aktif' => 'tidak aktif',
            ];

            $status = $statusMap[$request->status] ?? $cooperation->status;
            $primaryTipePelaksana = $tipePelaksana[0] ?? null;

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
                'tipe_pelaksana' => $primaryTipePelaksana,
                'jurusan_id' => in_array('jurusan', $tipePelaksana, true) && $request->pelaksana_jurusan_ids ? $request->pelaksana_jurusan_ids[0] : null,
                'upa_id' => in_array('upa', $tipePelaksana, true) && $request->pelaksana_upa_ids ? $request->pelaksana_upa_ids[0] : null,
                'pusat_id' => in_array('pusat', $tipePelaksana, true) && $request->pelaksana_pusat_ids ? $request->pelaksana_pusat_ids[0] : null,
            ]);

            $this->syncPksNumbers($cooperation, $request->input('pks_numbers', []));

            // 4. Handle Pivot Tables (Jurusan, UPA, Pusat)
            // Reset dulu agar tidak dobel
            $cooperation->jurusans()->detach();
            $cooperation->upas()->detach();
            $cooperation->pusats()->detach();
            $cooperation->prodis()->detach();

            $this->syncPelaksanaRelations($cooperation, $tipePelaksana, $request);

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
                            'indikator_id' => $detailData['indikator_id'] ?? null,
                            'output' => $detailData['output'] ?? null,
                            'outcome' => $detailData['outcome'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('unit.dkerjasama')->with('success', 'Data kerjasama berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $this->formatExceptionMessage($e));
        }
    }

    // ─── SUBMIT TO PIMPINAN ─────────────────────────────

    public function submitToPimpinan($id)
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return back()->with('error', 'Profil Anda tidak ditemukan.');
        }

        $cooperation = Cooperation::findOrFail($id);

        // Validasi kepemilikan data (Best Practice)
        // Jika user adalah unit_kerja, pastikan data ini milik unitnya (jika relasi sudah benar)
        // Untuk saat ini, kita cek apakah user memiliki unit_kerja_id atau jurusan_id
        if (!$profile->unit_kerja_id && !$profile->jurusan_id) {
            return back()->with('error', 'Anda tidak memiliki otoritas untuk mengirim data ini.');
        }

        // Pengiriman hanya boleh pada awal pengajuan atau setelah revisi.
        if (!in_array($cooperation->status_dokumen, ['Draft', 'Revisi'], true)) {
            return back()->with('error', 'Pengiriman ke Pimpinan hanya tersedia untuk status Draft atau Revisi.');
        }

        $kirimUlangSetelahRevisi = $cooperation->status_dokumen === 'Revisi';

        DB::beginTransaction();
        try {
            // 1. Update status_dokumen pada tabel cooperations menjadi ‘Menunggu Evaluasi’
            $cooperation->update([
                'status_dokumen' => 'Menunggu Evaluasi'
            ]);

            // 2. Ambil semua user dengan role 'pimpinan'
            $pimpinans = User::whereHas('role', function ($q) {
                $q->where('role_name', 'pimpinan');
            })->get();

            // 3. Simpan notifikasi secara otomatis ke tabel notifikasis
            $pesan = $kirimUlangSetelahRevisi
                ? "Dokumen kerjasama revisi: '{$cooperation->title}' telah dikirim ulang dan membutuhkan evaluasi dari Anda."
                : "Pengajuan kerjasama baru: '{$cooperation->title}' membutuhkan evaluasi dari Anda.";
            $judul = $kirimUlangSetelahRevisi ? 'Evaluasi Ulang Dokumen Revisi' : 'Persetujuan Kerjasama Baru';

            foreach ($pimpinans as $pimpinan) {
                Notifikasi::send(
                    $pimpinan->id,      // user_id (receiver)
                    $user->id,          // sender_id
                    $cooperation->id,   // source_id
                    $kirimUlangSetelahRevisi ? 'sudah_revisi' : 'evaluasi',
                    $judul,             // judul
                    $pesan,             // pesan
                    route('pimpinan.evaluasi') // link
                );
            }

            DB::commit();
            return back()->with('success', 'Berhasil! Data kerjasama telah dikirim ke Pimpinan untuk dievaluasi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim permintaan: ' . $this->formatExceptionMessage($e));
        }
    }

    // ─── DESTROY ─────────────────────────────────────────

    public function updateDocumentLink(Request $request, $id)
    {
        $request->validate([
            'document_link' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile || !$profile->unit_kerja_id) {
            return response()->json([
                'message' => 'Profil unit kerja tidak ditemukan.',
            ], 403);
        }

        $cooperation = Cooperation::findOrFail($id);
        $cooperation->update([
            'document_link' => $request->document_link,
        ]);

        return response()->json([
            'message' => 'Link dokumen berhasil disimpan.',
            'document_link' => $cooperation->document_link,
        ]);
    }

    public function destroy($id)
    {
        $kegiatan = Cooperation::findOrFail($id);

        try {
            $deleted = $kegiatan->deleteWithUnusedPejabats();

            if (! $deleted) {
                return back()->with('error', 'Data kerjasama gagal dihapus. Tidak ada perubahan yang disimpan.');
            }
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Data kerjasama gagal dihapus. Tidak ada perubahan yang disimpan.');
        }

        return redirect()->route('unit.dkerjasama')->with('success', 'Data kerjasama berhasil dihapus.');
    }

    private function formatExceptionMessage(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Data too long for column')) {
            return 'Ada isian yang melebihi batas panjang kolom database. Untuk Volume Luaran dan Satuan Luaran, gunakan teks singkat maksimal 255 karakter.';
        }

        if (str_contains($message, 'SQLSTATE')) {
            return 'Terjadi kendala database saat memproses data. Periksa kembali format, panjang isian, dan relasi data yang dipilih.';
        }

        return $message;
    }

    private function canRequestExtension(Cooperation $cooperation): bool
    {
        $status = strtolower($cooperation->status ?? '');
        $isExpiredStatus = in_array($status, ['kadarluarsa', 'kadaluarsa', 'kedaluwarsa'], true);
        $isInExtension = str_contains($status, 'perpanjangan');
        $isExpiredDate = false;
        $isNearExpiry = false;

        if ($cooperation->end_date) {
            $today = now()->startOfDay();
            $endDate = \Carbon\Carbon::parse($cooperation->end_date)->startOfDay();
            $isExpiredDate = $today->greaterThan($endDate);
            $isNearExpiry = !$isExpiredDate && $today->diffInDays($endDate) <= 30;
        }

        return $cooperation->status_dokumen === 'Disahkan'
            && !$isInExtension
            && ($isExpiredStatus || $isExpiredDate || $isNearExpiry);
    }

    private function requiresPelaksana(?string $jenis): bool
    {
        return in_array($jenis, [
            'MoA (Memorandum of Agreement)',
            'IA (Implementation Agreement)',
        ], true);
    }

    private function normalizedTipePelaksana($types): array
    {
        return collect((array) $types)
            ->filter(fn ($type) => in_array($type, ['jurusan', 'upa', 'pusat'], true))
            ->unique()
            ->values()
            ->all();
    }

    private function syncPelaksanaRelations(Cooperation $cooperation, array $tipePelaksana, Request $request): void
    {
        if (in_array('jurusan', $tipePelaksana, true)) {
            $cooperation->jurusans()->sync((array) $request->input('pelaksana_jurusan_ids', []));
            $cooperation->prodis()->sync((array) $request->input('pelaksana_prodi_ids', []));
        }

        if (in_array('upa', $tipePelaksana, true)) {
            $cooperation->upas()->sync((array) $request->input('pelaksana_upa_ids', []));
        }

        if (in_array('pusat', $tipePelaksana, true)) {
            $cooperation->pusats()->sync((array) $request->input('pelaksana_pusat_ids', []));
        }
    }

    private function syncPksNumbers(Cooperation $cooperation, array $numbers): void
    {
        $cleanNumbers = $this->normalizedPksNumbers($numbers)->unique()->values();

        $cooperation->pksNumbers()->delete();

        $cleanNumbers->each(function (string $number, int $index) use ($cooperation) {
            $cooperation->pksNumbers()->create([
                'number' => $number,
                'sort_order' => $index,
            ]);
        });
    }

    private function normalizedPksNumbers(array $numbers)
    {
        return collect($numbers)
            ->map(fn ($number) => trim((string) $number))
            ->filter()
            ->values();
    }
}
