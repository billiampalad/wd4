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
        Schema::table('mitras', function (Blueprint $table) {
            $table->foreignId('id_klasifikasi')->after('id')->nullable()->constrained('klasifikasi')->onDelete('set null');
            $table->string('alamat', 255)->after('nama_mitra')->nullable();
            $table->string('telp', 20)->after('negara')->nullable();
            $table->string('website', 255)->after('telp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitras', function (Blueprint $table) {
            $table->dropForeign(['id_klasifikasi']);
            $table->dropColumn(['id_klasifikasi', 'alamat', 'telp', 'website']);
        });
    }
};
