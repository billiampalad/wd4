<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. DAFTAR RENAME TABEL JIKA DITEMUKAN NAMA SINGULAR/LAMA
        $renameMap = [
            'role' => 'roles',
            'klasifikasi' => 'klasifikasis',
            'jurusan' => 'jurusans',
            'upa' => 'upas',
            'pusat' => 'pusats',
            'unit_kerja' => 'unit_kerjas',
            'pejabat' => 'pejabats',
            'sasaran' => 'sasarans',
            'jenis_kerjasama' => 'jenis_kerjasamas',
            'mitra' => 'mitras',
            'prodi' => 'prodis',
            'profile' => 'profiles',
            'notifikasi' => 'notifikasis',
            'pengajuan_kerjasama_barus' => 'pengajuan_kerjasama_baru',
            'pengajuan_perpanjangan_kerjasamas' => 'pengajuan_perpanjangan_kerjasama',
            'cooperation' => 'cooperations',
            'kerjasama' => 'cooperations',
            'kerjasamas' => 'cooperations',
            'pks_number' => 'pks_numbers',
            'laporan_file' => 'laporan_files',
            'kerjasama_jurusan' => 'cooperation_jurusan',
            'kerjasama_prodi' => 'cooperation_prodi',
            'kerjasama_upa' => 'cooperation_upa',
            'kerjasama_pusat' => 'cooperation_pusat',
            'indikator' => 'indikators',
            'kegiatan_kerjasama' => 'kegiatan_kerjasamas',
            'detail_kegiatan' => 'detail_kegiatans',
            'mahasiswa' => 'mahasiswas',
            'kegiatan_mahasiswa' => 'kegiatan_mahasiswas',
            'pembimbing' => 'pembimbings',
            'evaluasi' => 'evaluasis',
            'alumni' => 'alumnis',
            'alumni_mitra' => 'alumni_mitras',
            'showcase_image' => 'showcase_images',
        ];

        foreach ($renameMap as $old => $new) {
            // Hindari rename jika $old adalah MySQL VIEW
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                try {
                    Schema::rename($old, $new);
                } catch (\Throwable $e) {
                    // Abaikan jika berupa view atau constraint
                }
            }
        }

        // 2. DAFTAR STRUKTUR TABEL & KOLOM
        $tableSchemas = [
            'roles' => [
                'role_name' => 'string:255',
                'description' => 'text',
            ],
            'klasifikasis' => [
                'nama' => 'string:100',
                'keterangan' => 'text',
            ],
            'jurusans' => [
                'kode_jurusan' => 'string:20',
                'nama_jurusan' => 'string:150',
            ],
            'upas' => [
                'nama_upa' => 'string:150',
                'keterangan' => 'text',
            ],
            'pusats' => [
                'nama_pusat' => 'string:150',
                'keterangan' => 'text',
            ],
            'unit_kerjas' => [
                'nama_unit_pelaksana' => 'string:255',
                'nama_unit_kerja' => 'string:255',
                'keterangan' => 'text',
            ],
            'pejabats' => [
                'nama' => 'string:255',
                'jabatan' => 'string:255',
                'nip' => 'string:50',
            ],
            'sasarans' => [
                'deskripsi' => 'string:255',
            ],
            'jenis_kerjasamas' => [
                'nama' => 'string:255',
                'nama_kerjasama' => 'string:255',
                'keterangan' => 'text',
            ],
            'password_reset_tokens' => [
                'email' => 'string:255',
                'token' => 'string:255',
            ],
            'mitras' => [
                'id_klasifikasi' => 'unsignedBigInteger',
                'nama_mitra' => 'string:255',
                'alamat' => 'string:255',
                'kota' => 'string:100',
                'kecamatan' => 'string:100',
                'kelurahan' => 'string:100',
                'negara' => 'string:255',
                'country_code' => 'string:2',
                'provinsi' => 'string:120',
                'province_code' => 'string:10',
                'telepon' => 'string:50',
                'website' => 'string:255',
                'status_akses' => 'string:50:default_Pending',
            ],
            'prodis' => [
                'jurusan_id' => 'unsignedBigInteger',
                'kode_prodi' => 'string:20',
                'nama_prodi' => 'string:150',
                'jenjang' => 'string:20:default_D4',
            ],
            'users' => [
                'nik' => 'string:50',
                'name' => 'string:255',
                'email' => 'string:255',
                'email_verified_at' => 'timestamp',
                'password' => 'string:255',
                'role_id' => 'unsignedBigInteger',
                'is_active' => 'boolean:default_1',
                'mitra_id' => 'unsignedBigInteger',
                'remember_token' => 'string:100',
            ],
            'profiles' => [
                'user_id' => 'unsignedBigInteger',
                'jabatan' => 'string:255',
                'jurusan_id' => 'unsignedBigInteger',
                'prodi_id' => 'unsignedBigInteger',
                'upa_id' => 'unsignedBigInteger',
                'pusat_id' => 'unsignedBigInteger',
                'unit_kerja_id' => 'unsignedBigInteger',
            ],
            'notifikasis' => [
                'user_id' => 'unsignedBigInteger',
                'title' => 'string:255',
                'message' => 'text',
                'url' => 'string:255',
                'is_read' => 'boolean:default_0',
                'sender_id' => 'unsignedBigInteger',
                'source_id' => 'unsignedBigInteger',
                'source_type' => 'string:255',
                'type' => 'string:255',
            ],
            'pengajuan_kerjasama_baru' => [
                'kode_pengajuan' => 'string:255',
                'nama_mitra' => 'string:255',
                'id_klasifikasi' => 'unsignedBigInteger',
                'kategori' => 'string:100:default_nasional',
                'negara' => 'string:255',
                'alamat' => 'text',
                'telp' => 'string:50',
                'website' => 'string:255',
                'nama_penandatangan' => 'string:255',
                'jabatan_penandatangan' => 'string:255',
                'nama_penanggung_jawab' => 'string:255',
                'jabatan_penanggung_jawab' => 'string:255',
                'email' => 'string:255',
                'judul_pengajuan' => 'string:255',
                'tujuan_pengajuan' => 'text',
                'ruang_lingkup' => 'text',
                'pesan_tambahan' => 'text',
                'status' => 'string:50:default_diajukan',
                'catatan_pimpinan' => 'text',
                'reviewed_by' => 'unsignedBigInteger',
                'reviewed_at' => 'timestamp',
                'submitted_at' => 'timestamp',
                'mitra_id' => 'unsignedBigInteger',
                'user_id' => 'unsignedBigInteger',
                'jenis' => 'string:50',
                'judul' => 'string:255',
                'catatan' => 'text',
                'file_draft' => 'string:255',
            ],
            'pengajuan_perpanjangan_kerjasama' => [
                'kode_pengajuan' => 'string:255',
                'mitra_id' => 'unsignedBigInteger',
                'nama_mitra' => 'string:255',
                'id_klasifikasi' => 'unsignedBigInteger',
                'kategori' => 'string:100:default_nasional',
                'negara' => 'string:255',
                'alamat' => 'text',
                'telp' => 'string:50',
                'website' => 'string:255',
                'nama_penandatangan' => 'string:255',
                'jabatan_penandatangan' => 'string:255',
                'nama_penanggung_jawab' => 'string:255',
                'jabatan_penanggung_jawab' => 'string:255',
                'email' => 'string:255',
                'jenis' => 'string:255',
                'doc_number' => 'string:255',
                'start_date' => 'date',
                'end_date' => 'date',
                'file_surat' => 'string:255',
                'judul_pengajuan' => 'string:255',
                'tujuan_pengajuan' => 'text',
                'ruang_lingkup' => 'text',
                'pesan_tambahan' => 'text',
                'status' => 'string:50:default_diajukan',
                'catatan_pimpinan' => 'text',
                'reviewed_by' => 'unsignedBigInteger',
                'reviewed_at' => 'timestamp',
                'submitted_at' => 'timestamp',
                'cooperation_id' => 'unsignedBigInteger',
                'user_id' => 'unsignedBigInteger',
                'alasan_perpanjangan' => 'text',
                'catatan' => 'text',
                'file_pendukung' => 'string:255',
            ],
            'cooperations' => [
                'parent_cooperation_id' => 'unsignedBigInteger',
                'mitra_id' => 'unsignedBigInteger',
                'internal_instansi' => 'string:255:default_Politeknik Negeri Manado',
                'penandatangan_internal_id' => 'unsignedBigInteger',
                'pj_internal_id' => 'unsignedBigInteger',
                'penandatangan_mitra_id' => 'unsignedBigInteger',
                'pj_mitra_id' => 'unsignedBigInteger',
                'jenis' => 'string:50:default_MoU',
                'doc_number' => 'string:255',
                'judul' => 'string:255',
                'ruang_lingkup' => 'text',
                'start_date' => 'date',
                'end_date' => 'date',
                'status_berlaku' => 'string:50:default_Aktif',
                'status_dokumen' => 'string:50:default_Draft',
                'perpanjangan_dari_id' => 'unsignedBigInteger',
                'pengajuan_kerjasama_baru_id' => 'unsignedBigInteger',
                'pengajuan_perpanjangan_kerjasama_id' => 'unsignedBigInteger',
                'tingkat' => 'string:50:default_Institusi',
                'jurusan_id' => 'unsignedBigInteger',
                'upa_id' => 'unsignedBigInteger',
                'pusat_id' => 'unsignedBigInteger',
                'document_link' => 'string:255',
                'catatan_pimpinan' => 'text',
                'created_by' => 'unsignedBigInteger',
                'updated_by' => 'unsignedBigInteger',
            ],
            'pks_numbers' => [
                'cooperation_id' => 'unsignedBigInteger',
                'number' => 'string:255',
                'sort_order' => 'integer:default_0',
                'nomor_pihak_kampus' => 'string:100',
                'nomor_pihak_mitra' => 'string:100',
                'pks_number' => 'string:255',
            ],
            'laporan_files' => [
                'unit_kerja_id' => 'unsignedBigInteger',
                'jurusan_id' => 'unsignedBigInteger',
                'upa_id' => 'unsignedBigInteger',
                'pusat_id' => 'unsignedBigInteger',
                'cooperation_id' => 'unsignedBigInteger',
                'uploaded_by' => 'unsignedBigInteger',
                'uploader_role' => 'string:30',
                'file_path' => 'string:255',
                'original_name' => 'string:255',
                'file_size' => 'unsignedBigInteger:default_0',
            ],
            'cooperation_jurusan' => [
                'cooperation_id' => 'unsignedBigInteger',
                'jurusan_id' => 'unsignedBigInteger',
            ],
            'cooperation_prodi' => [
                'cooperation_id' => 'unsignedBigInteger',
                'prodi_id' => 'unsignedBigInteger',
            ],
            'cooperation_upa' => [
                'cooperation_id' => 'unsignedBigInteger',
                'upa_id' => 'unsignedBigInteger',
            ],
            'cooperation_pusat' => [
                'cooperation_id' => 'unsignedBigInteger',
                'pusat_id' => 'unsignedBigInteger',
            ],
            'indikators' => [
                'sasaran_id' => 'unsignedBigInteger',
                'nama_indikator' => 'string:255',
            ],
            'kegiatan_kerjasamas' => [
                'cooperation_id' => 'unsignedBigInteger',
                'nama_kegiatan' => 'string:255',
                'periode_mulai' => 'date',
                'periode_selesai' => 'date',
                'status' => 'string:50:default_Perencanaan',
            ],
            'detail_kegiatans' => [
                'kegiatan_kerjasama_id' => 'unsignedBigInteger',
                'cooperation_id' => 'unsignedBigInteger',
                'jenis_kerjasama_id' => 'unsignedBigInteger',
                'sasaran_id' => 'unsignedBigInteger',
                'indikator_id' => 'unsignedBigInteger',
                'income' => 'string:255',
                'volume_luaran' => 'string:255',
                'satuan_luaran' => 'string:100',
                'keterangan_luaran' => 'text',
                'output' => 'text',
                'outcome' => 'text',
                'nilai_kontrak' => 'string:255',
            ],
            'mahasiswas' => [
                'nim' => 'string:255',
                'nama' => 'string:255',
                'prodi_id' => 'unsignedBigInteger',
                'angkatan' => 'integer',
                'email' => 'string:255',
                'telepon' => 'string:255',
                'status' => 'string:50:default_Aktif',
            ],
            'kegiatan_mahasiswas' => [
                'kegiatan_id' => 'unsignedBigInteger',
                'mahasiswa_id' => 'unsignedBigInteger',
                'mitra_id' => 'unsignedBigInteger',
                'periode_mulai' => 'date',
                'periode_selesai' => 'date',
                'status' => 'string:50:default_Aktif',
                'nilai_mitra' => 'decimal:5:2',
                'catatan_mitra' => 'text',
            ],
            'pembimbings' => [
                'kegiatan_mahasiswa_id' => 'unsignedBigInteger',
                'nama_pembimbing' => 'string:255',
                'tipe' => 'string:50:default_Internal',
                'kontak' => 'string:255',
            ],
            'evaluasis' => [
                'cooperation_id' => 'unsignedBigInteger',
                'evaluator_id' => 'unsignedBigInteger',
                'tipe_evaluasi' => 'string:50:default_Internal',
                'score' => 'decimal:5:2',
                'realisasi_volume' => 'integer',
                'realisasi_output' => 'text',
                'realisasi_outcome' => 'text',
                'sesuai_rencana' => 'tinyInteger',
                'kualitas' => 'tinyInteger',
                'keterlibatan' => 'tinyInteger',
                'efisiensi' => 'tinyInteger',
                'kepuasan' => 'tinyInteger',
                'kendala' => 'text',
                'ringkasan' => 'text',
                'rekomendasi' => 'text',
                'kesimpulan' => 'string:100',
                'tindak_lanjut' => 'text',
                'status_validasi' => 'string:50:default_Draft',
            ],
            'alumnis' => [
                'nim' => 'string:255',
                'nama' => 'string:255',
                'prodi_id' => 'unsignedBigInteger',
                'tahun_lulus' => 'integer',
                'email' => 'string:255',
                'telepon' => 'string:255',
            ],
            'alumni_mitras' => [
                'alumni_id' => 'unsignedBigInteger',
                'mitra_id' => 'unsignedBigInteger',
                'posisi' => 'string:255',
                'tahun_mulai' => 'integer',
                'status' => 'string:255:default_Aktif',
                'sumber_data' => 'string:255',
            ],
            'showcase_images' => [
                'judul' => 'string:150',
                'image_path' => 'string:255',
            ],
        ];

        // 3. EKSEKUSI PEMERIKSAAN SETIAP TABEL & KOLOM
        foreach ($tableSchemas as $tableName => $columns) {
            if (!Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($columns) {
                    $table->id();
                    foreach ($columns as $col => $def) {
                        $this->applyColumnDefinition($table, $col, $def);
                    }
                    $table->timestamps();
                });
            } else {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                    foreach ($columns as $col => $def) {
                        if (!Schema::hasColumn($tableName, $col)) {
                            $this->applyColumnDefinition($table, $col, $def, true);
                        }
                    }
                    if (!Schema::hasColumn($tableName, 'created_at')) {
                        $table->timestamps();
                    }
                });
            }
        }
    }

    /**
     * Helper to apply column definition dynamically
     */
    private function applyColumnDefinition(Blueprint $table, string $name, string $definition, bool $forceNullable = false): void
    {
        $parts = explode(':', $definition);
        $type = $parts[0];

        $col = null;
        if (isset($parts[1]) && is_numeric($parts[1])) {
            $len = (int)$parts[1];
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $dec = (int)$parts[2];
                $col = $table->{$type}($name, $len, $dec);
            } else {
                $col = $table->{$type}($name, $len);
            }
        } else {
            $col = $table->{$type}($name);
        }

        if ($forceNullable || in_array('nullable', $parts) || true) {
            $col->nullable();
        }

        foreach ($parts as $part) {
            if (strpos($part, 'default_') === 0) {
                $defaultVal = substr($part, 8);
                if (is_numeric($defaultVal)) {
                    $col->default($defaultVal + 0);
                } else {
                    $col->default($defaultVal);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to preserve production data
    }
};
