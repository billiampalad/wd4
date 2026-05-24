<?php

namespace App\Support;

use App\Models\Jurusan;
use App\Models\Profile;
use App\Models\Pusat;
use App\Models\Upa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CooperationAccess
{
    public static function profileForUser(User $user): Profile
    {
        $profile = Profile::with(['jurusan', 'unitKerja'])->where('user_id', $user->id)->first();

        if (!$profile) {
            abort(403, 'Profil pengguna tidak ditemukan.');
        }

        return $profile;
    }

    public static function scopeForProfile(Builder $query, Profile $profile): Builder
    {
        $accesses = self::accessesForProfile($profile);

        if ($accesses->isEmpty()) {
            abort(403, 'Profil Anda belum terhubung dengan jurusan, UPA, atau pusat yang valid.');
        }

        return $query->where(function (Builder $scope) use ($accesses) {
            foreach ($accesses as $access) {
                self::orWhereAccess($scope, $access['type'], $access['id']);
            }
        });
    }

    public static function requestMatchesProfile(Profile $profile, ?string $type, array $ids): bool
    {
        if (!$type) {
            return false;
        }

        $requestedIds = collect($ids)->filter()->map(fn ($id) => (int) $id)->values();

        if ($requestedIds->isEmpty()) {
            return false;
        }

        return self::accessesForProfile($profile)
            ->where('type', $type)
            ->pluck('id')
            ->intersect($requestedIds)
            ->isNotEmpty();
    }

    public static function accessesForProfile(Profile $profile): Collection
    {
        $accesses = collect();

        if ($profile->jurusan_id) {
            $accesses->push(['type' => 'jurusan', 'id' => (int) $profile->jurusan_id]);
        }

        foreach (['upa', 'pusat'] as $type) {
            $id = $profile->getAttribute($type . '_id');

            if ($id) {
                $accesses->push(['type' => $type, 'id' => (int) $id]);
            }
        }

        $unitName = $profile->unitKerja?->nama_unit_pelaksana;
        if ($unitName) {
            $accesses = $accesses->merge(self::accessesFromUnitName($unitName));
        }

        return $accesses
            ->unique(fn (array $access) => $access['type'] . ':' . $access['id'])
            ->values();
    }

    private static function accessesFromUnitName(string $unitName): Collection
    {
        $normalizedUnitName = self::normalizeName($unitName);

        if ($normalizedUnitName === '') {
            return collect();
        }

        return collect()
            ->merge(
                Jurusan::query()
                    ->get(['id', 'nama_jurusan'])
                    ->filter(fn (Jurusan $jurusan) => self::normalizeName($jurusan->nama_jurusan) === $normalizedUnitName)
                    ->map(fn (Jurusan $jurusan) => ['type' => 'jurusan', 'id' => (int) $jurusan->id])
            )
            ->merge(
                Upa::query()
                    ->get(['id', 'nama_upa'])
                    ->filter(fn (Upa $upa) => self::normalizeName($upa->nama_upa) === $normalizedUnitName)
                    ->map(fn (Upa $upa) => ['type' => 'upa', 'id' => (int) $upa->id])
            )
            ->merge(
                Pusat::query()
                    ->get(['id', 'nama_pusat'])
                    ->filter(fn (Pusat $pusat) => self::normalizeName($pusat->nama_pusat) === $normalizedUnitName)
                    ->map(fn (Pusat $pusat) => ['type' => 'pusat', 'id' => (int) $pusat->id])
            );
    }

    private static function orWhereAccess(Builder $query, string $type, int $id): void
    {
        $column = $type . '_id';
        $relation = match ($type) {
            'jurusan' => 'jurusans',
            'upa' => 'upas',
            'pusat' => 'pusats',
        };

        $query->orWhere($column, $id)
            ->orWhereHas($relation, fn (Builder $relationQuery) => $relationQuery->whereKey($id));
    }

    private static function normalizeName(?string $name): string
    {
        return Str::of($name ?? '')
            ->squish()
            ->lower()
            ->toString();
    }
}
