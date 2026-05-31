<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            ! Schema::hasColumn('cooperations', 'created_by')
            || ! Schema::hasColumn('cooperations', 'updated_by')
        ) {
            return;
        }

        $fallbackUserId = $this->fallbackUserId();

        DB::table('cooperations')
            ->select('id', 'tipe_pelaksana', 'jurusan_id', 'upa_id', 'pusat_id', 'created_by', 'updated_by')
            ->whereNull('created_by')
            ->orWhereNull('updated_by')
            ->orderBy('id')
            ->chunkById(100, function ($cooperations) use ($fallbackUserId) {
                foreach ($cooperations as $cooperation) {
                    $userId = $this->resolveAuditUserId($cooperation) ?? $fallbackUserId;

                    if (! $userId) {
                        continue;
                    }

                    DB::table('cooperations')
                        ->where('id', $cooperation->id)
                        ->update([
                            'created_by' => $cooperation->created_by ?: $userId,
                            'updated_by' => $cooperation->updated_by ?: $userId,
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Backfilled audit history should not be removed on rollback.
    }

    private function resolveAuditUserId(object $cooperation): ?int
    {
        $type = strtolower((string) $cooperation->tipe_pelaksana);

        if ($type === 'jurusan' && $cooperation->jurusan_id) {
            return $this->userIdForProfileColumn('jurusan_id', $cooperation->jurusan_id);
        }

        if ($type === 'upa' && $cooperation->upa_id) {
            return $this->userIdForProfileColumn('upa_id', $cooperation->upa_id);
        }

        if ($type === 'pusat' && $cooperation->pusat_id) {
            return $this->userIdForProfileColumn('pusat_id', $cooperation->pusat_id);
        }

        return null;
    }

    private function userIdForProfileColumn(string $column, int $id): ?int
    {
        return DB::table('profiles')
            ->join('users', 'users.id', '=', 'profiles.user_id')
            ->where("profiles.{$column}", $id)
            ->orderBy('users.id')
            ->value('users.id');
    }

    private function fallbackUserId(): ?int
    {
        return DB::table('users')
            ->join('profiles', 'profiles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->whereNotNull('profiles.unit_kerja_id')
            ->orderByRaw("CASE WHEN LOWER(roles.role_name) = 'unit_kerja' THEN 0 ELSE 1 END")
            ->orderBy('users.id')
            ->value('users.id')
            ?: DB::table('users')->orderBy('id')->value('id');
    }
};
