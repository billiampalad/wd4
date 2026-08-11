<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 1/12: Tabel `roles`
 * - Rename kolom `role_name` → `name`
 * - Tambah kolom `display_name` VARCHAR(100) NOT NULL
 * - Tambah kolom `description` TEXT nullable
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Rename role_name → name
            $table->renameColumn('role_name', 'name');
        });

        Schema::table('roles', function (Blueprint $table) {
            // Tambah kolom display_name dan description
            $table->string('display_name', 100)->after('name')->nullable();
            $table->text('description')->after('display_name')->nullable();
        });

        // Isi display_name berdasarkan nama role yang umum
        $defaults = [
            'admin'    => 'Administrator',
            'pimpinan' => 'Pimpinan',
            'humas'    => 'Humas / WD4',
            'jurusan'  => 'Jurusan',
            'prodi'    => 'Program Studi',
            'upa'      => 'Unit Pelaksana Akademik',
            'pusat'    => 'Pusat Riset / Unit Khusus',
            'mitra'    => 'Mitra DUDIKA',
        ];

        foreach ($defaults as $name => $displayName) {
            DB::table('roles')
                ->where('name', $name)
                ->whereNull('display_name')
                ->update(['display_name' => $displayName]);
        }

        // Pastikan kolom name memiliki constraint unique (jika belum)
        $hasIndex = collect(DB::select("SHOW INDEX FROM roles WHERE Key_name = 'roles_name_unique'"))->isNotEmpty();
        if (! $hasIndex) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unique('name');
            });
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Hapus unique jika baru ditambahkan
            try {
                $table->dropUnique('roles_name_unique');
            } catch (\Exception $e) {
                // ignore
            }
            $table->dropColumn(['display_name', 'description']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->renameColumn('name', 'role_name');
        });
    }
};
