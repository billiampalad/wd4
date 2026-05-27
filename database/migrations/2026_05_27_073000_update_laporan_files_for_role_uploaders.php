<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->dropForeignIfExists('laporan_files', 'laporan_files_unit_kerja_id_foreign');

        DB::statement('ALTER TABLE laporan_files MODIFY unit_kerja_id BIGINT UNSIGNED NULL');

        Schema::table('laporan_files', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_files', 'jurusan_id')) {
                $table->foreignId('jurusan_id')->nullable()->after('unit_kerja_id')->constrained('jurusans')->nullOnDelete();
            }

            if (!Schema::hasColumn('laporan_files', 'upa_id')) {
                $table->foreignId('upa_id')->nullable()->after('jurusan_id')->constrained('upas')->nullOnDelete();
            }

            if (!Schema::hasColumn('laporan_files', 'pusat_id')) {
                $table->foreignId('pusat_id')->nullable()->after('upa_id')->constrained('pusats')->nullOnDelete();
            }

            if (!Schema::hasColumn('laporan_files', 'uploader_role')) {
                $table->string('uploader_role', 30)->nullable()->after('uploaded_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laporan_files', function (Blueprint $table) {
            if (Schema::hasColumn('laporan_files', 'pusat_id')) {
                $table->dropForeign(['pusat_id']);
                $table->dropColumn('pusat_id');
            }

            if (Schema::hasColumn('laporan_files', 'upa_id')) {
                $table->dropForeign(['upa_id']);
                $table->dropColumn('upa_id');
            }

            if (Schema::hasColumn('laporan_files', 'jurusan_id')) {
                $table->dropForeign(['jurusan_id']);
                $table->dropColumn('jurusan_id');
            }

            if (Schema::hasColumn('laporan_files', 'uploader_role')) {
                $table->dropColumn('uploader_role');
            }
        });
    }

    private function dropForeignIfExists(string $table, string $constraint): void
    {
        $database = DB::getDatabaseName();

        $exists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $constraint)
            ->exists();

        if ($exists) {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$constraint}");
        }
    }
};
