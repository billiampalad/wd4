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
        $table = 'users';
        $oldNames = array (
  0 => 'user',
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
            $table->string('nik', 50)->nullable()->unique();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->unsignedBigInteger('role_id');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('mitra_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 50)->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name', 255);
            }
            if (!Schema::hasColumn('users', 'email')) {
                $table->string('email', 255)->unique();
            }
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password', 255);
            }
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('users', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
