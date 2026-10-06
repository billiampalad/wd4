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
        if (!Schema::hasTable('klasifikasis')) {
            Schema::create('klasifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('klasifikasis', function (Blueprint $table) {
            if (!Schema::hasColumn('klasifikasis', 'nama')) {
                $table->string('nama', 100)->unique();
            }
            if (!Schema::hasColumn('klasifikasis', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klasifikasis');
    }
};
