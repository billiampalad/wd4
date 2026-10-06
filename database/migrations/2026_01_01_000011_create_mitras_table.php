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
        if (!Schema::hasTable('mitras')) {
            Schema::create('mitras', function (Blueprint $table) {
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
            $table->string('telepon', 20)->nullable();
            $table->string('website', 255)->nullable();
            $table->enum('status_akses', ['Pending', 'Aktif', 'Nonaktif'])->default('Pending');
            $table->timestamps();
            });
        } else {
            Schema::table('mitras', function (Blueprint $table) {
            if (!Schema::hasColumn('mitras', 'id_klasifikasi')) {
                $table->unsignedBigInteger('id_klasifikasi')->nullable();
            }
            if (!Schema::hasColumn('mitras', 'nama_mitra')) {
                $table->string('nama_mitra', 255);
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
                $table->string('country_code', 2)->nullable()->index();
            }
            if (!Schema::hasColumn('mitras', 'province_code')) {
                $table->string('province_code', 10)->nullable()->index();
            }
            if (!Schema::hasColumn('mitras', 'negara')) {
                $table->string('negara', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'telepon')) {
                $table->string('telepon', 20)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'website')) {
                $table->string('website', 255)->nullable();
            }
            if (!Schema::hasColumn('mitras', 'status_akses')) {
                $table->enum('status_akses', ['Pending', 'Aktif', 'Nonaktif'])->default('Pending');
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
