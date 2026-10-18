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
        if (Schema::hasTable('mitras') && !Schema::hasColumn('mitras', 'email')) {
            Schema::table('mitras', function (Blueprint $table) {
                $table->string('email')->nullable()->after('website');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mitras') && Schema::hasColumn('mitras', 'email')) {
            Schema::table('mitras', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
