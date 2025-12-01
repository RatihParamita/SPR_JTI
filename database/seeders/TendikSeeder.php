<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TendikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'tendik_id' => 1,
                'user_id'   => 3,
                'tendik_nama'  => 'Tendik 1',
                'tendik_nidn'  => '198222222222222222',
                'tendik_noHp'  => '082222222222',
            ],
        ];

        DB::table('m_tendik')->insert($data);
    }
}
