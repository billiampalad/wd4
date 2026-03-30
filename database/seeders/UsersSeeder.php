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
        //     ['role_name' => 'pimpinan'],
        //     ['role_name' => 'jurusan'],
        //     ['role_name' => 'unit_kerja'],
        //     ['role_name' => 'admin'],
        // ]);

        // Insert users
        // DB::table('users')->insert([
        //     [
        //         'nik' => '123456',
        //         'name' => 'Admin Unit',
        //         'password' => Hash::make('password'),
        //         'role_id' => 3
        //     ],
        //     [
        //         'nik' => '222222',
        //         'name' => 'Admin Jurusan',
        //         'password' => Hash::make('password'),
        //         'role_id' => 2
        //     ],
        //     [
        //         'nik' => '012460',
        //         'name' => 'Admin Pimpinan',
        //         'password' => Hash::make('password'),
        //         'role_id' => 1
        //     ],
        //     [
        //         'nik' => '120604',
        //         'name' => 'Admin',
        //         'password' => Hash::make('password'),
        //         'role_id' => 4
        //     ]
        // ]);

        // insert jurusans
        // DB::table('jurusans')->insert([
        //     [
        //         'nama_jurusan' => 'Teknik Elektro',
        //     ]
        // ]);

        // insert unit_kerjas
        // DB::table('unit_kerjas')->insert([
        //     [
        //         'nama_unit_pelaksana' => 'Ketua Unit',
        //     ]
        // ]);

        // Insert profiles
        DB::table('profiles')->insert([
            [
                'user_id' => 1,
                'jabatan' => null,
                'jurusan_id' => null,
                'unit_kerja_id' => 1
            ],
            [
                'user_id' => 2,
                'jabatan' => null,
                'jurusan_id' => 1,
                'unit_kerja_id' => null
            ],
            [
                'user_id' => 3,
                'jabatan' => 'Wakil Direktur IV',
                'jurusan_id' => null,
                'unit_kerja_id' => null
            ],
            [
                'user_id' => 4,
                'jabatan' => 'Kepala Amin',
                'jurusan_id' => null,
                'unit_kerja_id' => null
            ]
        ]);
    }
}