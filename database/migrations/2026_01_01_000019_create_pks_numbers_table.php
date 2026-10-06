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
        if (!Schema::hasTable('pks_numbers')) {
            Schema::create('pks_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->string('pks_number', 255);
            $table->string('number', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            });
        } else {
            Schema::table('pks_numbers', function (Blueprint $table) {
            if (!Schema::hasColumn('pks_numbers', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'pks_number')) {
                $table->string('pks_number', 255);
            }
            if (!Schema::hasColumn('pks_numbers', 'number')) {
                $table->string('number', 255)->nullable();
            }
            if (!Schema::hasColumn('pks_numbers', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pks_numbers');
    }
};
