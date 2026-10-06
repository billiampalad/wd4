<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tablesToDrop = [
            'hasils',
            'hasil',
            'kesimpulans',
            'kesimpulan',
            'pelaksanaans',
            'pelaksanaan',
            'permasalahan_solusis',
            'permasalahan_solusi',
            'tujuans',
            'tujuan',
            'dokumentasis',
            'dokumentasi',
            'kegiatan_mitras',
            'kegiatan_mitra',
            'pengajuan_kerjasama_mitras',
            'pengajuan_kerjasama_mitra',
        ];

        // Disable foreign key checks momentarily to allow safe drops
        Schema::disableForeignKeyConstraints();

        foreach ($tablesToDrop as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
