<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kelas_id' => 1,
                'prodi_id' => 1,
                'kelas_nama' => '1A',
            ],
            [
                'kelas_id' => 2,
                'prodi_id' => 2,
                'kelas_nama' => '2B',
            ],
            [
                'kelas_id' => 3,
                'prodi_id' => 3,
                'kelas_nama' => '3C',
            ],
            [
                'kelas_id' => 4,
                'prodi_id' => 1,
                'kelas_nama' => '4D',
            ],
            [
                'kelas_id' => 5,
                'prodi_id' => 2,
                'kelas_nama' => '5E',
            ],
            [
                'kelas_id' => 6,
                'prodi_id' => 3,
                'kelas_nama' => '1B',
            ],
        ];

        DB::table('m_kelas')->insert($data);
    }
}
