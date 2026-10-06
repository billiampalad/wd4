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
        $table = 'roles';
        $oldNames = array (
  0 => 'role',
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
            $table->string('role_name', 255)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'role_name')) {
                $table->string('role_name', 255)->unique();
            }
            if (!Schema::hasColumn('roles', 'description')) {
                $table->text('description')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
