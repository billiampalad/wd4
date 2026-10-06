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

            $table->foreign('id_klasifikasi')->references('id')->on('klasifikasis')->onDelete('set null');
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