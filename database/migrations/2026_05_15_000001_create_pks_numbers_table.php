<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pks_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cooperation_id')->constrained('cooperations')->cascadeOnDelete();
            $table->string('number');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('number');
            $table->index(['cooperation_id', 'sort_order']);
        });

        if (Schema::hasColumn('cooperations', 'pks_number')) {
            DB::table('cooperations')
                ->select(['id', 'pks_number'])
                ->whereNotNull('pks_number')
                ->where('pks_number', '<>', '')
                ->orderBy('id')
                ->chunkById(100, function ($cooperations) {
                    foreach ($cooperations as $cooperation) {
                        $number = trim($cooperation->pks_number);

                        if ($number === '') {
                            continue;
                        }

                        DB::table('pks_numbers')->insertOrIgnore([
                            'cooperation_id' => $cooperation->id,
                            'number' => $number,
                            'sort_order' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                });

            $this->dropUniqueIndexIfExists('cooperations', 'pks_number');

            Schema::table('cooperations', function (Blueprint $table) {
                $table->dropColumn('pks_number');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('cooperations', 'pks_number')) {
            Schema::table('cooperations', function (Blueprint $table) {
                $table->string('pks_number')->nullable()->after('doc_number');
            });
        }

        DB::table('pks_numbers')
            ->select(['cooperation_id', 'number'])
            ->orderBy('cooperation_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->unique('cooperation_id')
            ->each(function ($number) {
                DB::table('cooperations')
                    ->where('id', $number->cooperation_id)
                    ->update(['pks_number' => $number->number]);
            });

        Schema::dropIfExists('pks_numbers');
    }

    private function dropUniqueIndexIfExists(string $table, string $column): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $indexes = DB::select(
            'SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND NON_UNIQUE = 0',
            [$table, $column]
        );

        foreach ($indexes as $index) {
            DB::statement(sprintf('ALTER TABLE `%s` DROP INDEX `%s`', $table, $index->INDEX_NAME));
        }
    }
};
