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
        // 1. Pastikan tabel fisik 'cooperation_jurusan' ada
        if (!Schema::hasTable('cooperation_jurusan')) {
            Schema::create('cooperation_jurusan', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cooperation_id');
                $table->unsignedBigInteger('jurusan_id');
                $table->timestamps();
            });
        }

        // 2. Pastikan tabel fisik 'cooperation_prodi' ada
        if (!Schema::hasTable('cooperation_prodi')) {
            Schema::create('cooperation_prodi', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cooperation_id');
                $table->unsignedBigInteger('prodi_id');
                $table->timestamps();
            });
        }

        // 3. Pastikan tabel fisik 'cooperation_upa' ada
        if (!Schema::hasTable('cooperation_upa')) {
            Schema::create('cooperation_upa', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cooperation_id');
                $table->unsignedBigInteger('upa_id');
                $table->timestamps();
            });
        }

        // 4. Pastikan tabel fisik 'cooperation_pusat' ada
        if (!Schema::hasTable('cooperation_pusat')) {
            Schema::create('cooperation_pusat', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cooperation_id');
                $table->unsignedBigInteger('pusat_id');
                $table->timestamps();
            });
        }

        // 5. Buat VIEW atau Tabel Kompatibilitas untuk kerjasama_jurusan
        $this->createViewOrTable(
            'kerjasama_jurusan',
            'cooperation_jurusan',
            ['id', 'cooperation_id', 'jurusan_id', 'created_at', 'updated_at']
        );

        // 6. Buat VIEW atau Tabel Kompatibilitas untuk kerjasama_prodi
        $this->createViewOrTable(
            'kerjasama_prodi',
            'cooperation_prodi',
            ['id', 'cooperation_id', 'prodi_id', 'created_at', 'updated_at']
        );

        // 7. Buat VIEW atau Tabel Kompatibilitas untuk kerjasama_upa
        $this->createViewOrTable(
            'kerjasama_upa',
            'cooperation_upa',
            ['id', 'cooperation_id', 'upa_id', 'created_at', 'updated_at']
        );

        // 8. Buat VIEW atau Tabel Kompatibilitas untuk kerjasama_pusat
        $this->createViewOrTable(
            'kerjasama_pusat',
            'cooperation_pusat',
            ['id', 'cooperation_id', 'pusat_id', 'created_at', 'updated_at']
        );

        // 9. Buat VIEW atau Tabel Kompatibilitas untuk klasifikasi
        if (Schema::hasTable('klasifikasis')) {
            $this->createViewOrTable(
                'klasifikasi',
                'klasifikasis',
                ['id', 'nama', 'keterangan', 'created_at', 'updated_at']
            );
        }
    }

    /**
     * Helper to create MySQL VIEW or fallback Table
     */
    private function createViewOrTable(string $aliasName, string $sourceTable, array $columns): void
    {
        $colsStr = implode(', ', array_map(fn($c) => "`{$c}`", $columns));

        $viewCreated = false;
        try {
            // Coba buat/replace VIEW
            DB::statement("CREATE OR REPLACE VIEW `{$aliasName}` AS SELECT {$colsStr} FROM `{$sourceTable}`");
            $viewCreated = true;
        } catch (\Throwable $e) {
            // Jika VIEW gagal dibuat (misal permission hosting), kita buat tabel fisik
            $viewCreated = false;
        }

        if (!$viewCreated && !Schema::hasTable($aliasName)) {
            try {
                // Buat tabel fisik sebagai fallback
                DB::statement("CREATE TABLE `{$aliasName}` AS SELECT * FROM `{$sourceTable}`");
            } catch (\Throwable $e2) {
                // Ignore jika tabel sudah ada
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
