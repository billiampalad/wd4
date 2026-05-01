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
        Schema::table('cooperations', function (Blueprint $table) {
            $table->string('tipe_pelaksana')->nullable()->after('status');
            $table->unsignedBigInteger('jurusan_id')->nullable()->after('tipe_pelaksana');
            $table->unsignedBigInteger('upa_id')->nullable()->after('jurusan_id');
            $table->unsignedBigInteger('pusat_id')->nullable()->after('upa_id');

            $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('set null');
            $table->foreign('upa_id')->references('id')->on('upas')->onDelete('set null');
            $table->foreign('pusat_id')->references('id')->on('pusats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
            $table->dropForeign(['upa_id']);
            $table->dropForeign(['pusat_id']);
            $table->dropColumn(['tipe_pelaksana', 'jurusan_id', 'upa_id', 'pusat_id']);
        });
    }
};
