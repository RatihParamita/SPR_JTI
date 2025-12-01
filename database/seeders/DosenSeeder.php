<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'dosen_id' => 1,
                'user_id'   => 2,
                'prodi_id'  => 1,
                'dosen_nama'  => 'Dosen 1',
                'dosen_nidn'  => '198111111111111111',
                'dosen_noHp'  => '08111111111',
            ],
        ];

        DB::table('m_dosen')->insert($data);
    }
}
