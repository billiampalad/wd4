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
        if (Schema::hasTable('cooperations')) {
            if (!Schema::hasColumn('cooperations', 'description')) {
                Schema::table('cooperations', function (Blueprint $table) {
                    $table->text('description')->nullable();
                });
            }

            if (Schema::hasColumn('cooperations', 'ruang_lingkup') && Schema::hasColumn('cooperations', 'description')) {
                DB::table('cooperations')
                    ->whereNotNull('ruang_lingkup')
                    ->where(function ($q) {
                        $q->whereNull('description')->orWhere('description', '');
                    })
                    ->update(['description' => DB::raw('ruang_lingkup')]);

                Schema::table('cooperations', function (Blueprint $table) {
                    $table->dropColumn('ruang_lingkup');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('cooperations')) {
            if (!Schema::hasColumn('cooperations', 'ruang_lingkup')) {
                Schema::table('cooperations', function (Blueprint $table) {
                    $table->text('ruang_lingkup')->nullable();
                });

                if (Schema::hasColumn('cooperations', 'description')) {
                    DB::table('cooperations')
                        ->whereNotNull('description')
                        ->update(['ruang_lingkup' => DB::raw('description')]);
                }
            }
        }
    }
};
