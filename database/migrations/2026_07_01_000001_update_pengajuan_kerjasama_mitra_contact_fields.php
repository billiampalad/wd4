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
        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'nama_pengaju')) {
                $table->renameColumn('nama_pengaju', 'nama_penandatangan');
            }

            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'jabatan_pengaju')) {
                $table->renameColumn('jabatan_pengaju', 'jabatan_penandatangan');
            }

            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'email_pengaju')) {
                $table->renameColumn('email_pengaju', 'email');
            }

            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'telepon_pengaju')) {
                $table->renameColumn('telepon_pengaju', 'telepon');
            }
        });

        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            if (! Schema::hasColumn('pengajuan_kerjasama_mitras', 'nama_penanggung_jawab')) {
                $table->string('nama_penanggung_jawab')->nullable()->after('jabatan_penandatangan');
            }

            if (! Schema::hasColumn('pengajuan_kerjasama_mitras', 'jabatan_penanggung_jawab')) {
                $table->string('jabatan_penanggung_jawab')->nullable()->after('nama_penanggung_jawab');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'jabatan_penanggung_jawab')) {
                $table->dropColumn('jabatan_penanggung_jawab');
            }

            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'nama_penanggung_jawab')) {
                $table->dropColumn('nama_penanggung_jawab');
            }
        });

        Schema::table('pengajuan_kerjasama_mitras', function (Blueprint $table) {
            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'nama_penandatangan')) {
                $table->renameColumn('nama_penandatangan', 'nama_pengaju');
            }

            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'jabatan_penandatangan')) {
                $table->renameColumn('jabatan_penandatangan', 'jabatan_pengaju');
            }

            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'email')) {
                $table->renameColumn('email', 'email_pengaju');
            }

            if (Schema::hasColumn('pengajuan_kerjasama_mitras', 'telepon')) {
                $table->renameColumn('telepon', 'telepon_pengaju');
            }
        });
    }
};