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
        $table = 'notifikasis';
        $oldNames = array (
  0 => 'notifikasi',
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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('judul', 255);
            $table->text('pesan');
            $table->string('tipe', 50)->default('info');
            $table->string('url', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            });
        } else {
            // 3. Tambahkan kolom yang belum ada jika tabel sudah ada
            Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('notifikasis', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'judul')) {
                $table->string('judul', 255);
            }
            if (!Schema::hasColumn('notifikasis', 'pesan')) {
                $table->text('pesan');
            }
            if (!Schema::hasColumn('notifikasis', 'tipe')) {
                $table->string('tipe', 50)->default('info');
            }
            if (!Schema::hasColumn('notifikasis', 'url')) {
                $table->string('url', 255)->nullable();
            }
            if (!Schema::hasColumn('notifikasis', 'is_read')) {
                $table->boolean('is_read')->default(false);
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
