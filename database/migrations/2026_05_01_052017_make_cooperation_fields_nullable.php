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
            $table->string('doc_number')->nullable()->change();
            $table->string('pks_number')->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cooperations', function (Blueprint $table) {
            $table->string('doc_number')->nullable(false)->change();
            $table->string('pks_number')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
};
