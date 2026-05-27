<?php

namespace App\Support;

use App\Models\Cooperation;
use App\Models\Notifikasi;
use App\Models\User;

class UnitKerjaNotifications
{
    public static function sendNewCooperation(Cooperation $cooperation, User $sender, string $sourceRole): void
    {
        $roleLabel = match ($sourceRole) {
            'jurusan' => 'Jurusan',
            'upa' => 'UPA',
            'pusat' => 'Pusat',
            default => 'Unit',
        };

        $title = $cooperation->title ?: 'Kerjasama Tanpa Judul';
        $judul = "Data Kerjasama Baru dari {$roleLabel}";
        $pesan = "{$roleLabel} menambahkan data kerjasama baru: '{$title}'.";
        $link = route('unit.kerjasama.show', $cooperation->id);

        User::whereHas('role', fn($query) => $query->where('role_name', 'unit_kerja'))
            ->whereKeyNot($sender->id)
            ->get()
            ->each(function (User $unitUser) use ($sender, $cooperation, $judul, $pesan, $link) {
                Notifikasi::send(
                    $unitUser->id,
                    $sender->id,
                    $cooperation->id,
                    'data_baru',
                    $judul,
                    $pesan,
                    $link
                );
            });
    }
}
