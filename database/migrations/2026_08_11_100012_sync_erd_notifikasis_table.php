<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ERD Sync Migrasi 12/12: Tabel `notifikasis`
 * - Rename `judul` → `title`
 * - Rename `pesan`  → `message`
 * - Rename `link`   → `url`
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            // Rename judul → title
            if (Schema::hasColumn('notifikasis', 'judul') &&
                ! Schema::hasColumn('notifikasis', 'title')) {
                $table->renameColumn('judul', 'title');
            }

            // Rename pesan → message
            if (Schema::hasColumn('notifikasis', 'pesan') &&
                ! Schema::hasColumn('notifikasis', 'message')) {
                $table->renameColumn('pesan', 'message');
            }
        });

        // Rename link → url (terpisah karena Doctrine butuh satu operasi per transaksi)
        Schema::table('notifikasis', function (Blueprint $table) {
            if (Schema::hasColumn('notifikasis', 'link') &&
                ! Schema::hasColumn('notifikasis', 'url')) {
                $table->renameColumn('link', 'url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            if (Schema::hasColumn('notifikasis', 'title') &&
                ! Schema::hasColumn('notifikasis', 'judul')) {
                $table->renameColumn('title', 'judul');
            }
            if (Schema::hasColumn('notifikasis', 'message') &&
                ! Schema::hasColumn('notifikasis', 'pesan')) {
                $table->renameColumn('message', 'pesan');
            }
        });

        Schema::table('notifikasis', function (Blueprint $table) {
            if (Schema::hasColumn('notifikasis', 'url') &&
                ! Schema::hasColumn('notifikasis', 'link')) {
                $table->renameColumn('url', 'link');
            }
        });
    }
};
