<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * ERD Sync Migrasi 4/12: Tabel `users`
 * - Jadikan kolom `nik` nullable (sesuai ERD: nullable untuk user mitra)
 * - Jadikan kolom `email` NOT NULL (sesuai ERD)
 * - Perbaiki ON DELETE `role_id` FK: cascade → restrict
 */
return new class extends Migration
{
    public function up(): void
    {
        // Isi email kosong/null dengan nilai default sebelum ubah ke NOT NULL
        DB::table('users')
            ->where(function ($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            ->orderBy('id')
            ->each(function ($user) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['email' => 'user' . $user->id . '@polnado.ac.id']);
            });

        Schema::table('users', function (Blueprint $table) {
            // Jadikan nik nullable
            $table->string('nik', 50)->nullable()->change();

            // Jadikan email NOT NULL (setelah data diisi di atas)
            $table->string('email', 255)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 50)->nullable(false)->change();
            $table->string('email', 255)->nullable()->change();
        });
    }
};
