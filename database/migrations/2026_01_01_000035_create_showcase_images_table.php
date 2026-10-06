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
        if (!Schema::hasTable('showcase_images')) {
            Schema::create('showcase_images', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255)->nullable();
            $table->string('image_path', 255);
            $table->timestamps();
            });
        } else {
            Schema::table('showcase_images', function (Blueprint $table) {
            if (!Schema::hasColumn('showcase_images', 'judul')) {
                $table->string('judul', 255)->nullable();
            }
            if (!Schema::hasColumn('showcase_images', 'image_path')) {
                $table->string('image_path', 255);
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showcase_images');
    }
};
