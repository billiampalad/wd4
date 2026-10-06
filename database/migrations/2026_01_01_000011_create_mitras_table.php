<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table = 'mitras';
        $oldNames = array (
  0 => 'mitra',
);

        // 1. Rename tabel lama jika tabel lama ditemukan dan tabel baru belum ada
        foreach ($oldNames as $old) {
            if (Schema::hasTable($old) && !Schema::hasTable($table)) {
                Schema::rename($old, $table);
                break;
            }
        }

        // 2. Buat tabel jika belum ada
        if (!Schema::hasTable($table)) {
            Schema::create($table, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_klasifikasi')->nullable();
            $table->string('nama_mitra', 255);
            $table->string('alamat', 255)->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('negara', 255)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('provinsi', 120)->nullable();
            $table->string('province_code', 10)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('status_akses', 50)->nullable()->default('Pending');
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
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
            if (!Schema::hasColumn('mitras', 'negara')) {
                $table->string('negara', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'country_code')) {
                $table->string('country_code', 2)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'provinsi')) {
                $table->string('provinsi', 120)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'province_code')) {
                $table->string('province_code', 10)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'telepon')) {
                $table->string('telepon', 50)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'website')) {
                $table->string('website', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'status_akses')) {
                $table->string('status_akses', 50)->nullable()->default('Pending');
            }
            if (!Schema::hasColumn('mitras', 'created_at')) {
                $table->timestamps();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitras');
    }
};
