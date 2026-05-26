<?php

namespace App\Http\Controllers\Pusat;

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
use App\Models\Notifikasi;
use App\Models\User;
use App\Support\CooperationAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KerjasamaPusatController extends Controller
{
    /**
     * Helper: get pusat ID from the logged-in user's profile.
     */
    private function getUnitId(): int
    {
        $profile = CooperationAccess::profileForUser(Auth::user());
        if (!$profile->pusat_id) {
            abort(403, 'Profil pusat tidak ditemukan.');
        }
        return (int) $profile->pusat_id;
    }

    private function currentProfile(): Profile
    {
        return CooperationAccess::profileForUser(Auth::user());
    }

    private function scopedCooperations()
    {
        return CooperationAccess::scopeForProfile(Cooperation::query(), $this->currentProfile());
    }

    private function findOwnedCooperation($id): Cooperation
    {
        return $this->scopedCooperations()->findOrFail($id);
    }

    private function ensureRequestedPelaksanaIsOwned(Request $request): void
    {
        $profile = $this->currentProfile();
        $type = $request->input('tipe_pelaksana');
        $ids = match ($type) {
            'jurusan' => (array) $request->input('pelaksana_jurusan_ids', []),
            'upa' => (array) $request->input('pelaksana_upa_ids', []),
            'pusat' => (array) $request->input('pelaksana_pusat_ids', []),
            default => [],
        };

        if (!CooperationAccess::requestMatchesProfile($profile, $type, $ids)) {
            abort(403, 'Anda hanya dapat mengelola data kerjasama milik pusat Anda sendiri.');
        }
    }

    // ─── CREATE PAGE ─────────────────────────────────────

    public function create(Request $request)
    {
        $unitId = $this->getUnitId();
        $mitras = Mitra::orderBy('nama_mitra')->get();
        $jurusans = collect();
        $prodis = collect();
        $upas = collect();
        $pusats = Pusat::whereKey($unitId)->orderBy('nama_pusat')->get();
        $allowedTipePelaksana = 'pusat';
        $jenisKerjasama = JenisKerjasama::orderBy('nama_kerjasama')->get();
        $sasarans = Sasaran::orderBy('deskripsi')->get();
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
                'details',
                'pksNumbers',
            ])->whereKey($this->findOwnedCooperation((int) $request->query('perpanjangan_dari'))->id)->firstOrFail();

            if (!$this->canRequestExtension($perpanjanganAsal)) {
                return redirect()
                    ->route('pusat.kerjasama.show', $perpanjanganAsal->id)
                    ->with('error', 'Perpanjangan hanya dapat diajukan untuk dokumen yang sudah disahkan dan masa berlakunya kadaluarsa atau tersisa maksimal 30 hari.');
            }
        }

        return view('auth.pusat', compact('mitras', 'jurusans', 'prodis', 'upas', 'pusats', 'allowedTipePelaksana', 'jenisKerjasama', 'sasarans', 'perpanjanganAsal'));
    }

    // ─── STORE ───────────────────────────────────────────

    public function store(Request $request)
    {
        $request->merge([
            'pks_numbers' => $this->normalizedPksNumbers($request->input('pks_numbers', []))->all(),
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
            // Tipe pelaksana hanya wajib jika jenis BUKAN MoU
            'tipe_pelaksana' => 'required_if:jenis,MoA (Memorandum of Agreement),IA (Implementation Agreement)|nullable|string|in:jurusan,upa,pusat',

            // Penggiat validation
            'penggiat_mitra_ids' => 'required|array|min:1',
            'penggiat' => 'required|array|min:1',
        ], [
            'title.required' => 'Judul kerjasama wajib diisi.',
            'jenis.required' => 'Jenis dokumen wajib dipilih.',
            'tipe_pelaksana.required_if' => 'Tipe pelaksana wajib dipilih untuk dokumen MoA atau IA.',
            'penggiat_mitra_ids.required' => 'Minimal pilih satu instansi mitra.',
            'doc_number.unique' => 'Nomor dokumen sudah digunakan pada data kerjasama lain.',
            'pks_numbers.*.unique' => 'Nomor PKS sudah digunakan pada data kerjasama lain.',
            'pks_numbers.*.distinct' => 'Nomor PKS tidak boleh duplikat dalam satu dokumen.',
        ]);

        $perpanjanganDariId = $request->filled('perpanjangan_dari_id') ? (int) $request->perpanjangan_dari_id : null;
        if ($perpanjanganDariId) {
            $perpanjanganAsal = $this->findOwnedCooperation($perpanjanganDariId);

            if (!$this->canRequestExtension($perpanjanganAsal)) {
                return back()
                    ->withInput()
                    ->with('error', 'Perpanjangan hanya dapat diajukan untuk dokumen yang sudah disahkan dan masa berlakunya kadaluarsa atau tersisa maksimal 30 hari.');
            }
        }

        $this->ensureRequestedPelaksanaIsOwned($request);

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
                'tipe_pelaksana' => $request->tipe_pelaksana,
                'jurusan_id' => ($request->tipe_pelaksana === 'jurusan' && $request->pelaksana_jurusan_ids) ? $request->pelaksana_jurusan_ids[0] : null,
                'upa_id' => ($request->tipe_pelaksana === 'upa' && $request->pelaksana_upa_ids) ? $request->pelaksana_upa_ids[0] : null,
                'pusat_id' => ($request->tipe_pelaksana === 'pusat' && $request->pelaksana_pusat_ids) ? $request->pelaksana_pusat_ids[0] : null,
            ]);

            $this->syncPksNumbers($cooperation, $request->input('pks_numbers', []));

            // 4. Handle Pivot Tables
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
                            'indikator_kinerja' => $detailData['indikator_kinerja'] ?: null,
                            'output' => $detailData['output'] ?: null,
                            'outcome' => $detailData['outcome'] ?: null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('pusat.dkerjasama')->with('success', 'Data kerjasama berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
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
            'evaluasis.penilai',
            'laporanFiles',
            'pksNumbers',
        ])->whereKey($this->findOwnedCooperation($id)->id)->firstOrFail();

        return view('auth.pusat', compact('kegiatan'));
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
            'details',
            'pksNumbers',
        ])->whereKey($this->findOwnedCooperation($id)->id)->firstOrFail();
        $mitras = Mitra::orderBy('nama_mitra')->get();
        $jurusans = Jurusan::orderBy('nama_jurusan')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $upas = Upa::orderBy('nama_upa')->get();
        $pusats = Pusat::orderBy('nama_pusat')->get();
        $jenisKerjasama = JenisKerjasama::orderBy('nama_kerjasama')->get();
        $sasarans = Sasaran::orderBy('deskripsi')->get();

        return view('auth.pusat', compact('kegiatan', 'mitras', 'jurusans', 'prodis', 'upas', 'pusats', 'jenisKerjasama', 'sasarans'));
    }

    // ─── UPDATE ──────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $cooperation = $this->findOwnedCooperation($id);

        $request->merge([
            'pks_numbers' => $this->normalizedPksNumbers($request->input('pks_numbers', []))->all(),
        ]);

        $this->ensureRequestedPelaksanaIsOwned($request);

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
            'tipe_pelaksana' => 'required_if:jenis,MoA (Memorandum of Agreement),IA (Implementation Agreement)|nullable|string|in:jurusan,upa,pusat',
            'penggiat_mitra_ids' => 'required|array|min:1',
            'penggiat' => 'required|array|min:1',
        ], [
            'title.required' => 'Judul kerjasama wajib diisi.',
            'jenis.required' => 'Jenis dokumen wajib dipilih.',
            'tipe_pelaksana.required_if' => 'Tipe pelaksana wajib dipilih untuk dokumen MoA atau IA.',
            'penggiat_mitra_ids.required' => 'Minimal pilih satu instansi mitra.',
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
                'tipe_pelaksana' => $request->tipe_pelaksana,
                'jurusan_id' => ($request->tipe_pelaksana === 'jurusan' && $request->pelaksana_jurusan_ids) ? $request->pelaksana_jurusan_ids[0] : null,
                'upa_id' => ($request->tipe_pelaksana === 'upa' && $request->pelaksana_upa_ids) ? $request->pelaksana_upa_ids[0] : null,
                'pusat_id' => ($request->tipe_pelaksana === 'pusat' && $request->pelaksana_pusat_ids) ? $request->pelaksana_pusat_ids[0] : null,
            ]);

            $this->syncPksNumbers($cooperation, $request->input('pks_numbers', []));

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
                            'output' => $detailData['output'] ?? null,
                            'outcome' => $detailData['outcome'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('pusat.dkerjasama')->with('success', 'Data kerjasama berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
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

        $cooperation = $this->findOwnedCooperation($id);

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
            return back()->with('error', 'Gagal mengirim permintaan: ' . $e->getMessage());
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

        if (!$profile || !$profile->jurusan_id) {
            return response()->json([
                'message' => 'Profil jurusan tidak ditemukan.',
            ], 403);
        }

        $cooperation = $this->findOwnedCooperation($id);
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
        $kegiatan = $this->findOwnedCooperation($id);
        $kegiatan->delete();

        return redirect()->route('pusat.dkerjasama')->with('success', 'Data kerjasama berhasil dihapus.');
    }

    public function storeTujuan(Request $request, $id)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function updateTujuan(Request $request, $id, $tujuanId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function destroyTujuan($id, $tujuanId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function storePelaksanaan(Request $request, $id)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function updatePelaksanaan(Request $request, $id, $pelaksanaanId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function destroyPelaksanaan($id, $pelaksanaanId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function storeHasil(Request $request, $id)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function updateHasil(Request $request, $id, $hasilId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function destroyHasil($id, $hasilId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan detail kerjasama mengikuti form edit kerjasama.');
    }

    public function storeDokumentasi(Request $request, $id)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan dokumentasi mengikuti form edit kerjasama.');
    }

    public function updateDokumentasi(Request $request, $id, $dokId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan dokumentasi mengikuti form edit kerjasama.');
    }

    public function destroyDokumentasi($id, $dokId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan dokumentasi mengikuti form edit kerjasama.');
    }

    public function storePermasalahan(Request $request, $id)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan permasalahan mengikuti form edit kerjasama.');
    }

    public function updatePermasalahan(Request $request, $id, $masalahId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan permasalahan mengikuti form edit kerjasama.');
    }

    public function destroyPermasalahan($id, $masalahId)
    {
        $this->findOwnedCooperation($id);

        return back()->with('error', 'Pengelolaan permasalahan mengikuti form edit kerjasama.');
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
