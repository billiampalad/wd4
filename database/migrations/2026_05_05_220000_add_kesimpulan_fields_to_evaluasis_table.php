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
        Schema::table('evaluasis', function (Blueprint $table) {
            $table->text('ringkasan')->nullable()->after('catatan');
            $table->text('saran')->nullable()->after('ringkasan');
            $table->text('tindak_lanjut')->nullable()->after('saran');
            $table->string('status_validasi')->nullable()->after('tindak_lanjut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasis', function (Blueprint $table) {
            $table->dropColumn([
                'ringkasan',
                'saran',
                'tindak_lanjut',
                'status_validasi',
            ]);
        });
    }
};
