<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->string('source_type')->nullable()->after('source_id');
        });

        DB::table('notifikasis')
            ->whereNull('source_type')
            ->whereNotNull('source_id')
            ->update(['source_type' => 'cooperation']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropColumn('source_type');
        });
    }
};
