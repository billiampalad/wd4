<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['role_name' => 'admin'],
            ['role_name' => 'pimpinan'],
            ['role_name' => 'humas'],
            ['role_name' => 'jurusan'],
            ['role_name' => 'prodi'],
            ['role_name' => 'upa'],
            ['role_name' => 'pusat'],
            ['role_name' => 'mitra'],
        ]);
    }
}
