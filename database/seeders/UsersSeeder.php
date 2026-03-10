<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Insert roles
        // DB::table('roles')->insert([
        //     ['id' => 1, 'role_name' => 'pimpinan'],
        //     ['id' => 2, 'role_name' => 'jurusan'],
        //     ['id' => 3, 'role_name' => 'unit_kerja'],
        //     ['id' => 4, 'role_name' => 'admin'],
        // ]);

        // Insert users
        // DB::table('users')->insert([
        //     [
        //         'id' => 1,
        //         'nik' => '123456',
        //         'name' => 'Admin Unit',
        //         'password' => Hash::make('password'),
        //         'role_id' => 3
        //     ],
        //     [
        //         'id' => 2,
        //         'nik' => '222222',
        //         'name' => 'Admin Jurusan',
        //         'password' => Hash::make('password'),
        //         'role_id' => 2
        //     ],
        //     [
        //         'id' => 3,
        //         'nik' => '012460',
        //         'name' => 'Admin Unit',
        //         'password' => Hash::make('password'),
        //         'role_id' => 3
        //     ],
        //     [
        //         'id' => 4,
        //         'nik' => '120604',
        //         'name' => 'Admin',
        //         'password' => Hash::make('password'),
        //         'role_id' => 4
        //     ]
        // ]);

        // Insert profiles
        DB::table('profiles')->insert([
            [
                'user_id' => 1,
                'jabatan' => 'Wakil Direktur IV',
            ]
            // [
            //     'user_id' => 2,
            //     'jabatan' => 'Ketua Jurusan',
            //     'nama_jurusan' => 'Clynten Palad'
            // ],
            // [
            //     'user_id' => 3,
            //     'jabatan' => 'Ketua Unit',
            //     'nama_unit' => 'Clynten Palad'
            // ],
            // [
            //     'user_id' => 4,
            //     'jabatan' => 'Ketua Admin',
            //     'nama_unit' => 'Clynten Palad'
            // ]
        ]);
    }
}