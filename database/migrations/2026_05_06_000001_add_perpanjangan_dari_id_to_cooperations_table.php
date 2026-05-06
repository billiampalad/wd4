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
        if (! Schema::hasColumn('cooperations', 'perpanjangan_dari_id')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->foreignId('perpanjangan_dari_id')
                    ->nullable()
                    ->after('status_dokumen')
                    ->constrained('cooperations')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('cooperations', 'perpanjangan_dari_id')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->dropConstrainedForeignId('perpanjangan_dari_id');
            });
        }
    }
};
