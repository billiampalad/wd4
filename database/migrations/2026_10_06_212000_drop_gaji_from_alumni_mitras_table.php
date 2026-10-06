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
        if (Schema::hasTable('alumni_mitras') && Schema::hasColumn('alumni_mitras', 'gaji')) {
            Schema::table('alumni_mitras', function (Blueprint $table) {
                $table->dropColumn('gaji');
            });
        }

        if (Schema::hasTable('alumni_mitra') && Schema::hasColumn('alumni_mitra', 'gaji')) {
            Schema::table('alumni_mitra', function (Blueprint $table) {
                $table->dropColumn('gaji');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('alumni_mitras') && !Schema::hasColumn('alumni_mitras', 'gaji')) {
            Schema::table('alumni_mitras', function (Blueprint $table) {
                $table->decimal('gaji', 15, 2)->nullable();
            });
        }
    }
};
