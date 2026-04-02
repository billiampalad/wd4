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
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable()->constrained('users')->cascadeOnDelete()->after('user_id');
            $table->unsignedBigInteger('source_id')->nullable()->after('sender_id');
            $table->string('type')->nullable()->after('source_id'); // evaluasi, validasi, selesai
            $table->string('link')->nullable()->after('pesan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['sender_id', 'source_id', 'type', 'link']);
        });
    }
};
