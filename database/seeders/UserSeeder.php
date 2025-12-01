<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id'   => 1,
                'level_id'  => 1,
                'username'  => 'admin',
                'user_password'  => Hash::make('123456'),
            ],
            [
                'user_id'   => 2,
                'level_id'  => 2,
                'username'  => 'dosen',
                'user_password'  => Hash::make('123456')
            ],
            [
                'user_id'   => 3,
                'level_id'  => 3,
                'username'  => 'tendik',
                'user_password'  => Hash::make('123456')
            ],
            [
                'user_id'   => 4,
                'level_id'  => 4,
                'username'  => 'mahasiswa',
                'user_password'  => Hash::make('123456')
            ],
            [
                'user_id'   => 5,
                'level_id'  => 1,
                'username'  => '198000000000000000',
                'user_password'  => Hash::make('198000000000000000')
            ],
            [
                'user_id'   => 6,
                'level_id'  => 4,
                'username'  => '2241760063',
                'user_password'  => Hash::make('2241760063')
            ],
            
        ];

        DB::table('m_user')->insert($data);
    }
}
