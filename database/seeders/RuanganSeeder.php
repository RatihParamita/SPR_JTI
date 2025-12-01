<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'ruangan_id' => 1,
                'ruangan_kode' => 'R1',
                'ruangan_nama' => 'Ruangan 1',
                'ruangan_fasilitas' => 'AC, TV, dan kursi',
                'ruangan_kuota' => 10,
            ],
            [
                'ruangan_id' => 2,
                'ruangan_kode' => 'R2',
                'ruangan_nama' => 'Ruangan 2',
                'ruangan_fasilitas' => 'Meja dan proyektor',
                'ruangan_kuota' => 20,
            ],
            [
                'ruangan_id' => 3,
                'ruangan_kode' => 'R3',
                'ruangan_nama' => 'Ruangan 3',
                'ruangan_fasilitas' => 'Meja, kursi, dan lemari',
                'ruangan_kuota' => 30,
            ],
            [
                'ruangan_id' => 4,
                'ruangan_kode' => 'R4',
                'ruangan_nama' => 'Ruangan 4',
                'ruangan_fasilitas' => 'AC dan kursi',
                'ruangan_kuota' => 10,
            ],
            [
                'ruangan_id' => 5,
                'ruangan_kode' => 'R5',
                'ruangan_nama' => 'Ruangan 5',
                'ruangan_fasilitas' => 'Meja dan kursi',
                'ruangan_kuota' => 20,
            ],
            [
                'ruangan_id' => 6,
                'ruangan_kode' => 'R6',
                'ruangan_nama' => 'Ruangan 6',
                'ruangan_fasilitas' => 'Lemari, TV, dan proyektor',
                'ruangan_kuota' => 30,
            ],
        ];

        DB::table('m_ruangan')->insert($data);
    }
}
