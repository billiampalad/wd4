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
                $table->string('judul')->nullable();
                $table->string('image_path');
                $table->timestamps();
            });
        } else {
            Schema::table('showcase_images', function (Blueprint $table) {
                $columnsToDrop = [
                    'kategori',
                    'label_nav',
                    'deskripsi',
                    'lokasi_spesifik',
                    'highlight_meta',
                    'tags',
                    'urutan',
                    'is_active',
                    'created_by',
                ];

                $existingColumns = [];
                foreach ($columnsToDrop as $column) {
                    if (Schema::hasColumn('showcase_images', $column)) {
                        $existingColumns[] = $column;
                    }
                }

                if (!empty($existingColumns)) {
                    $table->dropColumn($existingColumns);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
