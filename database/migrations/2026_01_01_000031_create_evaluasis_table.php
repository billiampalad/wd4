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
        if (!Schema::hasTable('evaluasis')) {
            Schema::create('evaluasis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cooperation_id')->nullable();
            $table->unsignedBigInteger('evaluator_id')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('nilai')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('evaluasis', function (Blueprint $table) {
            if (!Schema::hasColumn('evaluasis', 'cooperation_id')) {
                $table->unsignedBigInteger('cooperation_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'evaluator_id')) {
                $table->unsignedBigInteger('evaluator_id')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'catatan')) {
                $table->text('catatan')->nullable();
            }
            if (!Schema::hasColumn('evaluasis', 'nilai')) {
                $table->integer('nilai')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasis');
    }
};
