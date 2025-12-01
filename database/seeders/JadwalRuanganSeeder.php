<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalRuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'jadwal_ruangan_id' => 1,
                'jadwal_id' => 1,
                'ruangan_id' => 2,
            ],
            [
                'jadwal_ruangan_id' => 2,
                'jadwal_id' => 2,
                'ruangan_id' => 2,
            ],
            [
                'jadwal_ruangan_id' => 3,
                'jadwal_id' => 3,
                'ruangan_id' => 3,
            ],
            [
                'jadwal_ruangan_id' => 4,
                'jadwal_id' => 3,
                'ruangan_id' => 4,
            ],
        ];

        DB::table('t_jadwal_ruangan')->insert($data);
    }
}
