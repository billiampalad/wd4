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
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role_name', 255)->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            });
        } else {
            Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'role_name')) {
                $table->string('role_name', 255)->unique();
            }
            if (!Schema::hasColumn('roles', 'description')) {
                $table->text('description')->nullable();
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
