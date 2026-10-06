<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to synchronize all tables and columns dynamically.
     */
    public function up(): void
    {
        // 1. Roles
        $this->syncTable('roles', ['role'], function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 255)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'role_name')) {
                $table->string('role_name', 255)->nullable();
            }
            if (!Schema::hasColumn('roles', 'description')) {
                $table->text('description')->nullable();
            }
        });

        // 2. Klasifikasis
        $this->syncTable('klasifikasis', ['klasifikasi'], function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('klasifikasis', 'nama')) {
                $table->string('nama', 100)->nullable();
            }
            if (!Schema::hasColumn('klasifikasis', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
        });

        // 3. Jurusans
        $this->syncTable('jurusans', ['jurusan'], function (Blueprint $table) {
            $table->id();
            $table->string('kode_jurusan', 20)->nullable()->unique();
            $table->string('nama_jurusan', 150)->unique();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('jurusans', 'kode_jurusan')) {
                $table->string('kode_jurusan', 20)->nullable();
            }
            if (!Schema::hasColumn('jurusans', 'nama_jurusan')) {
                $table->string('nama_jurusan', 150)->nullable();
            }
        });

        // 4. Upas
        $this->syncTable('upas', ['upa'], function (Blueprint $table) {
            $table->id();
            $table->string('nama_upa', 150)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('upas', 'nama_upa')) {
                $table->string('nama_upa', 150)->nullable();
            }
            if (!Schema::hasColumn('upas', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
        });

        // 5. Pusats
        $this->syncTable('pusats', ['pusat'], function (Blueprint $table) {
            $table->id();
            $table->string('nama_pusat', 150)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('pusats', 'nama_pusat')) {
                $table->string('nama_pusat', 150)->nullable();
            }
            if (!Schema::hasColumn('pusats', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
        });

        // 6. Unit Kerjas
        $this->syncTable('unit_kerjas', ['unit_kerja'], function (Blueprint $table) {
            $table->id();
            $table->string('nama_unit', 150)->nullable();
            $table->string('nama_unit_pelaksana', 150)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('unit_kerjas', 'nama_unit')) {
                $table->string('nama_unit', 150)->nullable();
            }
            if (!Schema::hasColumn('unit_kerjas', 'nama_unit_pelaksana')) {
                $table->string('nama_unit_pelaksana', 150)->nullable();
            }
            if (!Schema::hasColumn('unit_kerjas', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
        });

        // 7. Pejabats
        $this->syncTable('pejabats', ['pejabat'], function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('nip', 50)->nullable();
            $table->string('jabatan', 150)->nullable();
            $table->boolean('is_internal')->default(true);
            $table->string('instansi', 255)->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('pejabats', 'nama')) {
                $table->string('nama', 150)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'nip')) {
                $table->string('nip', 50)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'jabatan')) {
                $table->string('jabatan', 150)->nullable();
            }
            if (!Schema::hasColumn('pejabats', 'is_internal')) {
                $table->boolean('is_internal')->default(true);
            }
            if (!Schema::hasColumn('pejabats', 'instansi')) {
                $table->string('instansi', 255)->nullable();
            }
        });

        // 8. Sasarans
        $this->syncTable('sasarans', ['sasaran'], function (Blueprint $table) {
            $table->id();
            $table->string('nama_sasaran', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('sasarans', 'nama_sasaran')) {
                $table->string('nama_sasaran', 255)->nullable();
            }
            if (!Schema::hasColumn('sasarans', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
        });

        // 9. Jenis Kerjasamas
        $this->syncTable('jenis_kerjasamas', ['jenis_kerjasama'], function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->nullable();
            $table->string('nama_kerjasama', 150)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('jenis_kerjasamas', 'nama')) {
                $table->string('nama', 150)->nullable();
            }
            if (!Schema::hasColumn('jenis_kerjasamas', 'nama_kerjasama')) {
                $table->string('nama_kerjasama', 150)->nullable();
            }
            if (!Schema::hasColumn('jenis_kerjasamas', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
        });

        // 10. Password Reset Tokens
        $this->syncTable('password_reset_tokens', ['password_resets'], function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'token')) {
                $table->string('token')->nullable();
            }
        });

        // 11. Mitras
        $this->syncTable('mitras', ['mitra'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_klasifikasi')->nullable();
            $table->string('nama_mitra', 255);
            $table->string('alamat', 255)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('provinsi', 120)->nullable();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('province_code', 10)->nullable()->index();
            $table->string('negara', 255)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('telp', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->enum('status_akses', ['Pending', 'Aktif', 'Nonaktif'])->default('Pending');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('mitras', 'id_klasifikasi')) {
                $table->unsignedBigInteger('id_klasifikasi')->nullable();
            }
            if (!Schema::hasColumn('mitras', 'nama_mitra')) {
                $table->string('nama_mitra', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'alamat')) {
                $table->string('alamat', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'kota')) {
                $table->string('kota', 100)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'kelurahan')) {
                $table->string('kelurahan', 100)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'provinsi')) {
                $table->string('provinsi', 120)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'country_code')) {
                $table->string('country_code', 2)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'province_code')) {
                $table->string('province_code', 10)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'negara')) {
                $table->string('negara', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'telepon')) {
                $table->string('telepon', 50)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'telp')) {
                $table->string('telp', 50)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'website')) {
                $table->string('website', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'status_akses')) {
                $table->enum('status_akses', ['Pending', 'Aktif', 'Nonaktif'])->default('Pending');
            }
        });

        // 12. Prodis
        $this->syncTable('prodis', ['prodi'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->string('kode_prodi', 20)->nullable();
            $table->string('nama_prodi', 150);
            $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'Profesi'])->default('D4');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('prodis', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('prodis', 'kode_prodi')) {
                $table->string('kode_prodi', 20)->nullable();
            }
            if (!Schema::hasColumn('prodis', 'nama_prodi')) {
                $table->string('nama_prodi', 150)->nullable();
            }
            if (!Schema::hasColumn('prodis', 'jenjang')) {
                $table->enum('jenjang', ['D3', 'D4', 'S1', 'S2', 'Profesi'])->default('D4');
            }
        });

        // 13. Users
        $this->syncTable('users', ['user'], function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50)->nullable()->unique();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->unsignedBigInteger('role_id');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('users', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
        });

        // 14. Profiles
        $this->syncTable('profiles', ['profile'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('jabatan', 255)->nullable();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->unsignedBigInteger('upa_id')->nullable();
            $table->unsignedBigInteger('pusat_id')->nullable();
            $table->unsignedBigInteger('unit_kerja_id')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'jabatan')) {
                $table->string('jabatan', 255)->nullable();
            }
            if (!Schema::hasColumn('profiles', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'upa_id')) {
                $table->unsignedBigInteger('upa_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id')->nullable();
            }
            if (!Schema::hasColumn('profiles', 'unit_kerja_id')) {
                $table->unsignedBigInteger('unit_kerja_id')->nullable();
            }
        });

        // 15. Notifikasis
        $this->syncTable('notifikasis', ['notifikasi'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source_type', 100)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('message')->nullable();
            $table->string('judul', 255)->nullable();
            $table->text('pesan')->nullable();
            $table->string('tipe', 50)->default('info');
            $table->string('url', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('notifikasis', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'sender_id')) {
                $table->unsignedBigInteger('sender_id')->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'source_type')) {
                $table->string('source_type', 100)->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'type')) {
                $table->string('type', 50)->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'title')) {
                $table->string('title', 255)->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'message')) {
                $table->text('message')->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'judul')) {
                $table->string('judul', 255)->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'pesan')) {
                $table->text('pesan')->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'tipe')) {
                $table->string('tipe', 50)->default('info');
            }
            if (!Schema::hasColumn('notifikasis', 'url')) {
                $table->string('url', 255)->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'is_read')) {
                $table->boolean('is_read')->default(false);
            }
        });

        // 16. Pengajuan Kerjasama Baru
        $this->syncTable('pengajuan_kerjasama_baru', ['pengajuan_kerjasama_barus'], function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 100)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('nama_mitra', 255)->nullable();
            $table->unsignedBigInteger('id_klasifikasi')->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('negara', 100)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('telp', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('nama_penandatangan', 150)->nullable();
            $table->string('jabatan_penandatangan', 150)->nullable();
            $table->string('nama_penanggung_jawab', 150)->nullable();
            $table->string('jabatan_penanggung_jawab', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            $table->string('judul', 255)->nullable();
            $table->string('judul_pengajuan', 255)->nullable();
            $table->text('tujuan_pengajuan')->nullable();
            $table->text('ruang_lingkup')->nullable();
            $table->text('pesan_tambahan')->nullable();
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            $table->text('catatan')->nullable();
            $table->text('catatan_pimpinan')->nullable();
            $table->string('file_draft', 255)->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'kode_pengajuan')) {
                $table->string('kode_pengajuan', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'nama_mitra')) {
                $table->string('nama_mitra', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'id_klasifikasi')) {
                $table->unsignedBigInteger('id_klasifikasi')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'kategori')) {
                $table->string('kategori', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'negara')) {
                $table->string('negara', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'alamat')) {
                $table->string('alamat', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'telp')) {
                $table->string('telp', 50)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'website')) {
                $table->string('website', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'nama_penandatangan')) {
                $table->string('nama_penandatangan', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'jabatan_penandatangan')) {
                $table->string('jabatan_penandatangan', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'nama_penanggung_jawab')) {
                $table->string('nama_penanggung_jawab', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'jabatan_penanggung_jawab')) {
                $table->string('jabatan_penanggung_jawab', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'email')) {
                $table->string('email', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'jenis')) {
                $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'judul')) {
                $table->string('judul', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'judul_pengajuan')) {
                $table->string('judul_pengajuan', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'tujuan_pengajuan')) {
                $table->text('tujuan_pengajuan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'ruang_lingkup')) {
                $table->text('ruang_lingkup')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'pesan_tambahan')) {
                $table->text('pesan_tambahan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'status')) {
                $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'catatan_pimpinan')) {
                $table->text('catatan_pimpinan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'file_draft')) {
                $table->string('file_draft', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_kerjasama_baru', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }
        });

        // 17. Pengajuan Perpanjangan Kerjasama
        $this->syncTable('pengajuan_perpanjangan_kerjasama', ['pengajuan_perpanjangan_kerjasamas'], function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 100)->nullable();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('nama_mitra', 255)->nullable();
            $table->unsignedBigInteger('id_klasifikasi')->nullable();
            $table->string('kategori', 100)->nullable();
            $table->string('negara', 100)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('telp', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('nama_penandatangan', 150)->nullable();
            $table->string('jabatan_penandatangan', 150)->nullable();
            $table->string('nama_penanggung_jawab', 150)->nullable();
            $table->string('jabatan_penanggung_jawab', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('jenis', 50)->nullable();
            $table->string('doc_number', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('file_surat', 255)->nullable();
            $table->string('judul_pengajuan', 255)->nullable();
            $table->text('tujuan_pengajuan')->nullable();
            $table->text('ruang_lingkup')->nullable();
            $table->text('alasan_perpanjangan')->nullable();
            $table->text('pesan_tambahan')->nullable();
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            $table->text('catatan')->nullable();
            $table->text('catatan_pimpinan')->nullable();
            $table->string('file_pendukung', 255)->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'kode_pengajuan')) {
                $table->string('kode_pengajuan', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'nama_mitra')) {
                $table->string('nama_mitra', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'id_klasifikasi')) {
                $table->unsignedBigInteger('id_klasifikasi')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'kategori')) {
                $table->string('kategori', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'negara')) {
                $table->string('negara', 100)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'alamat')) {
                $table->string('alamat', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'telp')) {
                $table->string('telp', 50)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'website')) {
                $table->string('website', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'nama_penandatangan')) {
                $table->string('nama_penandatangan', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'jabatan_penandatangan')) {
                $table->string('jabatan_penandatangan', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'nama_penanggung_jawab')) {
                $table->string('nama_penanggung_jawab', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'jabatan_penanggung_jawab')) {
                $table->string('jabatan_penanggung_jawab', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'email')) {
                $table->string('email', 150)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'jenis')) {
                $table->string('jenis', 50)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'doc_number')) {
                $table->string('doc_number', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'file_surat')) {
                $table->string('file_surat', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'judul_pengajuan')) {
                $table->string('judul_pengajuan', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'tujuan_pengajuan')) {
                $table->text('tujuan_pengajuan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'ruang_lingkup')) {
                $table->text('ruang_lingkup')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'alasan_perpanjangan')) {
                $table->text('alasan_perpanjangan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'pesan_tambahan')) {
                $table->text('pesan_tambahan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'status')) {
                $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'])->default('Draft');
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'catatan_pimpinan')) {
                $table->text('catatan_pimpinan')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'file_pendukung')) {
                $table->string('file_pendukung', 255)->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'reviewed_by')) {
                $table->unsignedBigInteger('reviewed_by')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable();
            }
            if (!Schema::hasColumn('pengajuan_perpanjangan_kerjasama', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable();
            }
        });

        // 18. Cooperations
        $this->syncTable('cooperations', ['cooperation', 'kerjasama', 'kerjasamas'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_cooperation_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('internal_instansi', 255)->default('Politeknik Negeri Manado');
            $table->unsignedBigInteger('penandatangan_internal_id')->nullable();
            $table->unsignedBigInteger('pj_internal_id')->nullable();
            $table->unsignedBigInteger('penandatangan_mitra_id')->nullable();
            $table->unsignedBigInteger('pj_mitra_id')->nullable();
            $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            $table->string('doc_number', 255)->nullable();
            $table->string('judul', 255);
            $table->text('ruang_lingkup')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status_berlaku', ['Aktif', 'Kadaluarsa', 'Dalam Perpanjangan', 'Tidak Aktif'])->default('Aktif');
            $table->enum('status_dokumen', ['Draft', 'Menunggu Evaluasi', 'Disahkan', 'Revisi'])->default('Draft');
            $table->unsignedBigInteger('perpanjangan_dari_id')->nullable();
            $table->unsignedBigInteger('pengajuan_kerjasama_baru_id')->nullable();
            $table->unsignedBigInteger('pengajuan_perpanjangan_kerjasama_id')->nullable();
            $table->enum('tingkat', ['Institusi', 'Jurusan', 'Prodi', 'Pusat/UPA'])->default('Institusi');
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->unsignedBigInteger('upa_id')->nullable();
            $table->unsignedBigInteger('pusat_id')->nullable();
            $table->string('document_link', 255)->nullable();
            $table->text('catatan_pimpinan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('cooperations', 'parent_cooperation_id')) {
                $table->unsignedBigInteger('parent_cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'internal_instansi')) {
                $table->string('internal_instansi', 255)->default('Politeknik Negeri Manado');
            }
            if (!Schema::hasColumn('cooperations', 'penandatangan_internal_id')) {
                $table->unsignedBigInteger('penandatangan_internal_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pj_internal_id')) {
                $table->unsignedBigInteger('pj_internal_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'penandatangan_mitra_id')) {
                $table->unsignedBigInteger('penandatangan_mitra_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pj_mitra_id')) {
                $table->unsignedBigInteger('pj_mitra_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'jenis')) {
                $table->enum('jenis', ['MoU', 'MoA', 'IA', 'SPK'])->default('MoU');
            }
            if (!Schema::hasColumn('cooperations', 'doc_number')) {
                $table->string('doc_number', 255)->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'judul')) {
                $table->string('judul', 255)->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'ruang_lingkup')) {
                $table->text('ruang_lingkup')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'status_berlaku')) {
                $table->enum('status_berlaku', ['Aktif', 'Kadaluarsa', 'Dalam Perpanjangan', 'Tidak Aktif'])->default('Aktif');
            }
            if (!Schema::hasColumn('cooperations', 'status_dokumen')) {
                $table->enum('status_dokumen', ['Draft', 'Menunggu Evaluasi', 'Disahkan', 'Revisi'])->default('Draft');
            }
            if (!Schema::hasColumn('cooperations', 'perpanjangan_dari_id')) {
                $table->unsignedBigInteger('perpanjangan_dari_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pengajuan_kerjasama_baru_id')) {
                $table->unsignedBigInteger('pengajuan_kerjasama_baru_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pengajuan_perpanjangan_kerjasama_id')) {
                $table->unsignedBigInteger('pengajuan_perpanjangan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'tingkat')) {
                $table->enum('tingkat', ['Institusi', 'Jurusan', 'Prodi', 'Pusat/UPA'])->default('Institusi');
            }
            if (!Schema::hasColumn('cooperations', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'upa_id')) {
                $table->unsignedBigInteger('upa_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'document_link')) {
                $table->string('document_link', 255)->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'catatan_pimpinan')) {
                $table->text('catatan_pimpinan')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('cooperations', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }
        });

        // 19. Pks Numbers
        $this->syncTable('pks_numbers', ['pks_number'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->string('pks_number', 255)->nullable();
            $table->string('number', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('nomor_pihak_kampus', 255)->nullable();
            $table->string('nomor_pihak_mitra', 255)->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('pks_numbers', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'pks_number')) {
                $table->string('pks_number', 255)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'number')) {
                $table->string('number', 255)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
            if (!Schema::hasColumn('pks_numbers', 'nomor_pihak_kampus')) {
                $table->string('nomor_pihak_kampus', 255)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'nomor_pihak_mitra')) {
                $table->string('nomor_pihak_mitra', 255)->nullable();
            }
        });

        // 20. Laporan Files
        $this->syncTable('laporan_files', ['laporan_file'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('unit_kerja_id')->nullable();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->unsignedBigInteger('upa_id')->nullable();
            $table->unsignedBigInteger('pusat_id')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->string('uploader_role', 50)->nullable();
            $table->string('file_path', 255);
            $table->string('original_name', 255)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('nama_file', 255)->nullable();
            $table->string('tipe', 50)->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_files', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'unit_kerja_id')) {
                $table->unsignedBigInteger('unit_kerja_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'upa_id')) {
                $table->unsignedBigInteger('upa_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'uploaded_by')) {
                $table->unsignedBigInteger('uploaded_by')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'uploader_role')) {
                $table->string('uploader_role', 50)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'file_path')) {
                $table->string('file_path', 255)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'original_name')) {
                $table->string('original_name', 255)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'nama_file')) {
                $table->string('nama_file', 255)->nullable();
            }
            if (!Schema::hasColumn('laporan_files', 'tipe')) {
                $table->string('tipe', 50)->nullable();
            }
        });

        // 21. Pivot Units
        $this->syncTable('kerjasama_jurusan', ['cooperation_jurusan', 'kerjasama_jurusans'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('jurusan_id');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_jurusan', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kerjasama_jurusan', 'jurusan_id')) {
                $table->unsignedBigInteger('jurusan_id')->nullable();
            }
        });

        $this->syncTable('kerjasama_prodi', ['cooperation_prodi', 'kerjasama_prodis'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('prodi_id');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_prodi', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kerjasama_prodi', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
        });

        $this->syncTable('kerjasama_upa', ['cooperation_upa', 'kerjasama_upas'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('upa_id');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_upa', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kerjasama_upa', 'upa_id')) {
                $table->unsignedBigInteger('upa_id')->nullable();
            }
        });

        $this->syncTable('kerjasama_pusat', ['cooperation_pusat', 'kerjasama_pusats'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id');
            $table->unsignedBigInteger('pusat_id');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kerjasama_pusat', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kerjasama_pusat', 'pusat_id')) {
                $table->unsignedBigInteger('pusat_id')->nullable();
            }
        });

        // 25. Indikators
        $this->syncTable('indikators', ['indikator'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->string('nama_indikator', 255);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('indikators', 'sasaran_id')) {
                $table->unsignedBigInteger('sasaran_id')->nullable();
            }
            if (!Schema::hasColumn('indikators', 'nama_indikator')) {
                $table->string('nama_indikator', 255)->nullable();
            }
            if (!Schema::hasColumn('indikators', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
        });

        // 26. Kegiatan Kerjasamas
        $this->syncTable('kegiatan_kerjasamas', ['kegiatan_kerjasama'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->string('nama_kegiatan', 255);
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai'])->default('Perencanaan');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'nama_kegiatan')) {
                $table->string('nama_kegiatan', 255)->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'periode_mulai')) {
                $table->date('periode_mulai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'periode_selesai')) {
                $table->date('periode_selesai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_kerjasamas', 'status')) {
                $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai'])->default('Perencanaan');
            }
        });

        // 27. Detail Kegiatans
        $this->syncTable('detail_kegiatans', ['detail_kegiatan'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('jenis_kerjasama_id')->nullable();
            $table->unsignedBigInteger('sasaran_id')->nullable();
            $table->unsignedBigInteger('indikator_id')->nullable();
            $table->string('income', 255)->nullable();
            $table->string('volume_luaran', 255)->nullable();
            $table->string('satuan_luaran', 255)->nullable();
            $table->text('keterangan_luaran')->nullable();
            $table->text('output')->nullable();
            $table->text('outcome')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'jenis_kerjasama_id')) {
                $table->unsignedBigInteger('jenis_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'sasaran_id')) {
                $table->unsignedBigInteger('sasaran_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'indikator_id')) {
                $table->unsignedBigInteger('indikator_id')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'income')) {
                $table->string('income', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'volume_luaran')) {
                $table->string('volume_luaran', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'satuan_luaran')) {
                $table->string('satuan_luaran', 255)->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'keterangan_luaran')) {
                $table->text('keterangan_luaran')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'output')) {
                $table->text('output')->nullable();
            }
            if (!Schema::hasColumn('detail_kegiatans', 'outcome')) {
                $table->text('outcome')->nullable();
            }
        });

        // 28. Mahasiswas
        $this->syncTable('mahasiswas', ['mahasiswa'], function (Blueprint $table) {
            $table->id();
            $table->string('nim', 50)->unique();
            $table->string('nama', 255);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->string('angkatan', 10)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('status', 50)->nullable()->default('Aktif');
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('mahasiswas', 'nim')) {
                $table->string('nim', 50)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'nama')) {
                $table->string('nama', 255)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'angkatan')) {
                $table->string('angkatan', 10)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'email')) {
                $table->string('email', 150)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'telepon')) {
                $table->string('telepon', 50)->nullable();
            }
            if (!Schema::hasColumn('mahasiswas', 'status')) {
                $table->string('status', 50)->nullable();
            }
        });

        // 29. Kegiatan Mahasiswas
        $this->syncTable('kegiatan_mahasiswas', ['kegiatan_mahasiswa'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('kegiatan_id')->nullable();
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
            $table->unsignedBigInteger('detail_kegiatan_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->string('status', 50)->nullable();
            $table->decimal('nilai_mitra', 5, 2)->nullable();
            $table->text('catatan_mitra')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'kegiatan_id')) {
                $table->unsignedBigInteger('kegiatan_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'mahasiswa_id')) {
                $table->unsignedBigInteger('mahasiswa_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'detail_kegiatan_id')) {
                $table->unsignedBigInteger('detail_kegiatan_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'periode_mulai')) {
                $table->date('periode_mulai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'periode_selesai')) {
                $table->date('periode_selesai')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'status')) {
                $table->string('status', 50)->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'nilai_mitra')) {
                $table->decimal('nilai_mitra', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mahasiswas', 'catatan_mitra')) {
                $table->text('catatan_mitra')->nullable();
            }
        });

        // 30. Pembimbings
        $this->syncTable('pembimbings', ['pembimbing'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('kegiatan_mahasiswa_id')->nullable();
            $table->unsignedBigInteger('pejabat_id')->nullable();
            $table->string('nama_pembimbing', 150)->nullable();
            $table->string('tipe', 50)->nullable();
            $table->string('kontak', 50)->nullable();
            $table->string('peran', 255)->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('pembimbings', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'kegiatan_mahasiswa_id')) {
                $table->unsignedBigInteger('kegiatan_mahasiswa_id')->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'pejabat_id')) {
                $table->unsignedBigInteger('pejabat_id')->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'nama_pembimbing')) {
                $table->string('nama_pembimbing', 150)->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'tipe')) {
                $table->string('tipe', 50)->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'kontak')) {
                $table->string('kontak', 50)->nullable();
            }
            if (!Schema::hasColumn('pembimbings', 'peran')) {
                $table->string('peran', 255)->nullable();
            }
        });

        // 31. Evaluasis
        $this->syncTable('evaluasis', ['evaluasi'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('evaluator_id')->nullable();
            $table->string('tipe_evaluasi', 50)->nullable();
            $table->integer('score')->nullable();
            $table->string('realisasi_volume', 255)->nullable();
            $table->text('realisasi_output')->nullable();
            $table->text('realisasi_outcome')->nullable();
            $table->string('sesuai_rencana', 50)->nullable();
            $table->integer('kualitas')->nullable();
            $table->integer('keterlibatan')->nullable();
            $table->integer('efisiensi')->nullable();
            $table->integer('kepuasan')->nullable();
            $table->text('kendala')->nullable();
            $table->text('ringkasan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('kesimpulan')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->string('status_validasi', 50)->nullable()->default('Draft');
            $table->text('catatan')->nullable();
            $table->integer('nilai')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('evaluasis', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'evaluator_id')) {
                $table->unsignedBigInteger('evaluator_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'tipe_evaluasi')) {
                $table->string('tipe_evaluasi', 50)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'score')) {
                $table->integer('score')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_volume')) {
                $table->string('realisasi_volume', 255)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_output')) {
                $table->text('realisasi_output')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'realisasi_outcome')) {
                $table->text('realisasi_outcome')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'sesuai_rencana')) {
                $table->string('sesuai_rencana', 50)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kualitas')) {
                $table->integer('kualitas')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'keterlibatan')) {
                $table->integer('keterlibatan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'efisiensi')) {
                $table->integer('efisiensi')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kepuasan')) {
                $table->integer('kepuasan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kendala')) {
                $table->text('kendala')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'ringkasan')) {
                $table->text('ringkasan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'rekomendasi')) {
                $table->text('rekomendasi')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'kesimpulan')) {
                $table->text('kesimpulan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'tindak_lanjut')) {
                $table->text('tindak_lanjut')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'status_validasi')) {
                $table->string('status_validasi', 50)->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'nilai')) {
                $table->integer('nilai')->nullable();
            }
        });

        // 32. Alumnis
        $this->syncTable('alumnis', ['alumni'], function (Blueprint $table) {
            $table->id();
            $table->string('nim', 50);
            $table->string('nama', 255);
            $table->unsignedBigInteger('prodi_id')->nullable();
            $table->integer('tahun_lulus')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('alumnis', 'nim')) {
                $table->string('nim', 50)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'nama')) {
                $table->string('nama', 255)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'prodi_id')) {
                $table->unsignedBigInteger('prodi_id')->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'tahun_lulus')) {
                $table->integer('tahun_lulus')->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'email')) {
                $table->string('email', 150)->nullable();
            }
            if (!Schema::hasColumn('alumnis', 'telepon')) {
                $table->string('telepon', 50)->nullable();
            }
        });

        // 33. Alumni Mitras
        $this->syncTable('alumni_mitras', ['alumni_mitra'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumni_id')->nullable();
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->string('posisi', 255)->nullable();
            $table->decimal('gaji', 15, 2)->nullable();
            $table->integer('masa_tunggu_bulan')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('alumni_mitras', 'alumni_id')) {
                $table->unsignedBigInteger('alumni_id')->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'posisi')) {
                $table->string('posisi', 255)->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'gaji')) {
                $table->decimal('gaji', 15, 2)->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'masa_tunggu_bulan')) {
                $table->integer('masa_tunggu_bulan')->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'tanggal_mulai')) {
                $table->date('tanggal_mulai')->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable();
            }
            if (!Schema::hasColumn('alumni_mitras', 'status')) {
                $table->string('status', 50)->nullable();
            }
        });

        // 34. Dokumentasis
        $this->syncTable('dokumentasis', ['dokumentasi'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('link_drive', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('dokumentasis', 'kegiatan_kerjasama_id')) {
                $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable();
            }
            if (!Schema::hasColumn('dokumentasis', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('dokumentasis', 'file_path')) {
                $table->string('file_path', 255)->nullable();
            }
            if (!Schema::hasColumn('dokumentasis', 'link_drive')) {
                $table->string('link_drive', 255)->nullable();
            }
            if (!Schema::hasColumn('dokumentasis', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
        });

        // 35. Showcase Images
        $this->syncTable('showcase_images', ['showcase_image', 'showcase'], function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255)->nullable();
            $table->string('image_path', 255);
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('showcase_images', 'judul')) {
                $table->string('judul', 255)->nullable();
            }
            if (!Schema::hasColumn('showcase_images', 'image_path')) {
                $table->string('image_path', 255)->nullable();
            }
        });

        // 36. Hasils
        $this->syncTable('hasils', ['hasil'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->text('hasil_langsung')->nullable();
            $table->text('dampak')->nullable();
            $table->text('manfaat_mahasiswa')->nullable();
            $table->text('manfaat_polimdo')->nullable();
            $table->text('manfaat_mitra')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('hasils', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'hasil_langsung')) {
                $table->text('hasil_langsung')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'dampak')) {
                $table->text('dampak')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'manfaat_mahasiswa')) {
                $table->text('manfaat_mahasiswa')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'manfaat_polimdo')) {
                $table->text('manfaat_polimdo')->nullable();
            }
            if (!Schema::hasColumn('hasils', 'manfaat_mitra')) {
                $table->text('manfaat_mitra')->nullable();
            }
        });

        // 37. Pelaksanaans
        $this->syncTable('pelaksanaans', ['pelaksanaan'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('cakupan', 255)->nullable();
            $table->integer('jumlah_peserta')->nullable();
            $table->text('sumber_daya')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('pelaksanaans', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'cakupan')) {
                $table->string('cakupan', 255)->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'jumlah_peserta')) {
                $table->integer('jumlah_peserta')->nullable();
            }
            if (!Schema::hasColumn('pelaksanaans', 'sumber_daya')) {
                $table->text('sumber_daya')->nullable();
            }
        });

        // 38. Permasalahan Solusis
        $this->syncTable('permasalahan_solusis', ['permasalahan_solusi'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->text('kendala')->nullable();
            $table->text('solusi')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('permasalahan_solusis', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('permasalahan_solusis', 'kendala')) {
                $table->text('kendala')->nullable();
            }
            if (!Schema::hasColumn('permasalahan_solusis', 'solusi')) {
                $table->text('solusi')->nullable();
            }
            if (!Schema::hasColumn('permasalahan_solusis', 'rekomendasi')) {
                $table->text('rekomendasi')->nullable();
            }
        });

        // 39. Tujuans
        $this->syncTable('tujuans', ['tujuan'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->text('tujuan')->nullable();
            $table->text('sasaran')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('tujuans', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('tujuans', 'tujuan')) {
                $table->text('tujuan')->nullable();
            }
            if (!Schema::hasColumn('tujuans', 'sasaran')) {
                $table->text('sasaran')->nullable();
            }
        });

        // 40. Kegiatan Mitras
        $this->syncTable('kegiatan_mitras', ['kegiatan_mitra'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->unsignedBigInteger('id_mitra')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan_mitras', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('kegiatan_mitras', 'id_mitra')) {
                $table->unsignedBigInteger('id_mitra')->nullable();
            }
        });

        // 41. Kesimpulans
        $this->syncTable('kesimpulans', ['kesimpulan'], function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_kegiatan')->nullable();
            $table->text('ringkasan')->nullable();
            $table->text('saran')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();
        }, function (Blueprint $table) {
            if (!Schema::hasColumn('kesimpulans', 'id_kegiatan')) {
                $table->unsignedBigInteger('id_kegiatan')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'ringkasan')) {
                $table->text('ringkasan')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'saran')) {
                $table->text('saran')->nullable();
            }
            if (!Schema::hasColumn('kesimpulans', 'tindak_lanjut')) {
                $table->text('tindak_lanjut')->nullable();
            }
        });
    }

    /**
     * Helper to safely sync table renaming, creation, and column additions.
     */
    private function syncTable(string $table, array $oldNames, callable $createCallback, callable $updateCallback): void
    {
        // 1. Rename tabel lama jika ada
        foreach ($oldNames as $old) {
            if (Schema::hasTable($old) && !Schema::hasTable($table)) {
                Schema::rename($old, $table);
                break;
            }
        }

        // 2. Buat tabel jika belum ada
        if (!Schema::hasTable($table)) {
            Schema::create($table, $createCallback);
        } else {
            // 3. Tambahkan kolom jika tabel sudah ada tapi kolom belum ada
            Schema::table($table, $updateCallback);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible
    }
};
