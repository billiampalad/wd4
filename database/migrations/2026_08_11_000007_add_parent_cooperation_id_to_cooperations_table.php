<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            if (!Schema::hasColumn('cooperations', 'parent_cooperation_id')) {
                $table->foreignId('parent_cooperation_id')->nullable()->after('id')->constrained('cooperations')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->dropForeign(['parent_cooperation_id']);
            $table->dropColumn('parent_cooperation_id');
        });
    }
};
