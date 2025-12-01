<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'admin_id' => 1,
                'user_id'   => 1,
                'prodi_id'  => 1,
                'admin_nama'  => 'Admin 1',
                'admin_nidn'  => '198123456789012345',
                'admin_noHp'  => '08123456789',
            ],
            [
                'admin_id' => 2,
                'user_id'   => 5,
                'prodi_id'  => 2,
                'admin_nama'  => 'Admin 2',
                'admin_nidn'  => '198000000000000000',
                'admin_noHp'  => '08987654321',
            ],
        ];

        DB::table('m_admin')->insert($data);
    }
}
