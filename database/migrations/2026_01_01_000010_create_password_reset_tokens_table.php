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
        $table = 'password_reset_tokens';
        $oldNames = array (
  0 => 'password_resets',
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
            $table->string('email', 255);
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada (100% safe nullable alter)
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->string('email', 255)->nullable();
            }
            if (!Schema::hasColumn('password_reset_tokens', 'token')) {
                $table->string('token', 255)->nullable();
            }
            if (!Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
