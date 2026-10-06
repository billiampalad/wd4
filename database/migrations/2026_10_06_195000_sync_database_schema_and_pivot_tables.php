<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to synchronize schema, pivot tables, and columns safely.
     */
    public function up(): void
    {
        // 1. Sinkronisasi tabel 'roles'
        if (Schema::hasTable('roles')) {
            if (!Schema::hasColumn('roles', 'role_name')) {
                if (Schema::hasColumn('roles', 'name')) {
                    DB::statement("ALTER TABLE `roles` CHANGE COLUMN `name` `role_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL");
                } elseif (Schema::hasColumn('roles', 'slug')) {
                    DB::statement("ALTER TABLE `roles` CHANGE COLUMN `slug` `role_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL");
                } else {
                    Schema::table('roles', function (Blueprint $table) {
                        $table->string('role_name', 255)->after('id');
                    });
                }
            }
        }

        // 2. Sinkronisasi tabel 'klasifikasis'
        if (!Schema::hasTable('klasifikasis')) {
            if (Schema::hasTable('klasifikasi')) {
                Schema::rename('klasifikasi', 'klasifikasis');
            } else {
                Schema::create('klasifikasis', function (Blueprint $table) {
                    $table->id();
                    $table->string('nama', 100)->unique();
                    $table->text('keterangan')->nullable();
                    $table->timestamps();
                });
            }
        }

        // 3. Sinkronisasi tabel 'cooperations'
        if (Schema::hasTable('cooperations')) {
            Schema::table('cooperations', function (Blueprint $table) {
                if (!Schema::hasColumn('cooperations', 'judul')) {
                    $table->string('judul', 255)->default('-')->after('id');
                }
                if (!Schema::hasColumn('cooperations', 'doc_number')) {
                    $table->string('doc_number', 255)->nullable()->after('judul');
                }
                if (!Schema::hasColumn('cooperations', 'status_berlaku')) {
                    $table->enum('status_berlaku', ['Aktif', 'Kadaluarsa', 'Dalam Perpanjangan', 'Tidak Aktif'])->default('Aktif');
                }
                if (!Schema::hasColumn('cooperations', 'status_dokumen')) {
                    $table->enum('status_dokumen', ['Draft', 'Menunggu Evaluasi', 'Disahkan', 'Revisi'])->default('Draft');
                }
            });

            // Salin data jika judul masih kosong/default dan ada kolom lama
            if (Schema::hasColumn('cooperations', 'nama_kerjasama')) {
                DB::statement("UPDATE `cooperations` SET `judul` = `nama_kerjasama` WHERE (`judul` = '-' OR `judul` IS NULL) AND `nama_kerjasama` IS NOT NULL");
            }
            if (Schema::hasColumn('cooperations', 'judul_kerjasama')) {
                DB::statement("UPDATE `cooperations` SET `judul` = `judul_kerjasama` WHERE (`judul` = '-' OR `judul` IS NULL) AND `judul_kerjasama` IS NOT NULL");
            }
        }

        // 4. Sinkronisasi tabel pivot kerjasama (kerjasama_jurusan, kerjasama_prodi, kerjasama_upa, kerjasama_pusat)
        if (!Schema::hasTable('kerjasama_jurusan')) {
            if (Schema::hasTable('cooperation_jurusan')) {
                Schema::create('kerjasama_jurusan', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('jurusan_id');
                    $table->timestamps();
                });
                DB::statement("INSERT IGNORE INTO `kerjasama_jurusan` (`cooperation_id`, `jurusan_id`, `created_at`, `updated_at`) SELECT `cooperation_id`, `jurusan_id`, `created_at`, `updated_at` FROM `cooperation_jurusan`");
            } else {
                Schema::create('kerjasama_jurusan', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('jurusan_id');
                    $table->timestamps();
                });
            }
        }

        if (!Schema::hasTable('kerjasama_prodi')) {
            if (Schema::hasTable('cooperation_prodi')) {
                Schema::create('kerjasama_prodi', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('prodi_id');
                    $table->timestamps();
                });
                DB::statement("INSERT IGNORE INTO `kerjasama_prodi` (`cooperation_id`, `prodi_id`, `created_at`, `updated_at`) SELECT `cooperation_id`, `prodi_id`, `created_at`, `updated_at` FROM `cooperation_prodi`");
            } else {
                Schema::create('kerjasama_prodi', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('prodi_id');
                    $table->timestamps();
                });
            }
        }

        if (!Schema::hasTable('kerjasama_upa')) {
            if (Schema::hasTable('cooperation_upa')) {
                Schema::create('kerjasama_upa', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('upa_id');
                    $table->timestamps();
                });
                DB::statement("INSERT IGNORE INTO `kerjasama_upa` (`cooperation_id`, `upa_id`, `created_at`, `updated_at`) SELECT `cooperation_id`, `upa_id`, `created_at`, `updated_at` FROM `cooperation_upa`");
            } else {
                Schema::create('kerjasama_upa', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('upa_id');
                    $table->timestamps();
                });
            }
        }

        if (!Schema::hasTable('kerjasama_pusat')) {
            if (Schema::hasTable('cooperation_pusat')) {
                Schema::create('kerjasama_pusat', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('pusat_id');
                    $table->timestamps();
                });
                DB::statement("INSERT IGNORE INTO `kerjasama_pusat` (`cooperation_id`, `pusat_id`, `created_at`, `updated_at`) SELECT `cooperation_id`, `pusat_id`, `created_at`, `updated_at` FROM `cooperation_pusat`");
            } else {
                Schema::create('kerjasama_pusat', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id');
                    $table->unsignedBigInteger('pusat_id');
                    $table->timestamps();
                });
            }
        }

        // 5. Sinkronisasi tabel 'kegiatan_kerjasamas'
        if (!Schema::hasTable('kegiatan_kerjasamas')) {
            if (Schema::hasTable('kegiatan_kerjasama')) {
                Schema::rename('kegiatan_kerjasama', 'kegiatan_kerjasamas');
            } else {
                Schema::create('kegiatan_kerjasamas', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('cooperation_id')->nullable();
                    $table->string('nama_kegiatan', 255);
                    $table->date('periode_mulai')->nullable();
                    $table->date('periode_selesai')->nullable();
                    $table->enum('status', ['Perencanaan', 'Berjalan', 'Selesai'])->default('Perencanaan');
                    $table->timestamps();
                });
            }
        }

        // 6. Sinkronisasi tabel 'detail_kegiatans'
        if (!Schema::hasTable('detail_kegiatans')) {
            if (Schema::hasTable('detail_kegiatan')) {
                Schema::rename('detail_kegiatan', 'detail_kegiatans');
            } else {
                Schema::create('detail_kegiatans', function (Blueprint $table) {
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
                });
            }
        } else {
            Schema::table('detail_kegiatans', function (Blueprint $table) {
                if (!Schema::hasColumn('detail_kegiatans', 'kegiatan_kerjasama_id')) {
                    $table->unsignedBigInteger('kegiatan_kerjasama_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('detail_kegiatans', 'cooperation_id')) {
                    $table->unsignedBigInteger('cooperation_id')->nullable()->after('kegiatan_kerjasama_id');
                }
                if (!Schema::hasColumn('detail_kegiatans', 'satuan_luaran')) {
                    $table->string('satuan_luaran', 255)->nullable()->after('volume_luaran');
                }
            });

            if (Schema::hasColumn('detail_kegiatans', 'id_kegiatan_kerjasama')) {
                DB::statement("UPDATE `detail_kegiatans` SET `kegiatan_kerjasama_id` = `id_kegiatan_kerjasama` WHERE `kegiatan_kerjasama_id` IS NULL AND `id_kegiatan_kerjasama` IS NOT NULL");
            }
        }

        // 7. Sinkronisasi tabel 'mitras'
        if (Schema::hasTable('mitras')) {
            Schema::table('mitras', function (Blueprint $table) {
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
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe reversible migration
    }
};
