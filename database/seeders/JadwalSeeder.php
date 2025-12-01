<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'jadwal_id' => 1,
                'user_id'   => 2,
                'jadwal_nama'  => 'Jadwal 1',
                'jadwal_tgl'  => '2026-01-15',
                'jadwal_jam_mulai'  => '08:00:00',
                'jadwal_jam_selesai'  => '09:00:00',
                'jadwal_jumPes' => 15,
            ],
            [
                'jadwal_id' => 2,
                'user_id'   => 4,
                'jadwal_nama'  => 'Jadwal 2',
                'jadwal_tgl'  => '2026-01-15',
                'jadwal_jam_mulai'  => '09:00:00',
                'jadwal_jam_selesai'  => '10:00:00',
                'jadwal_jumPes' => 20,
            ],
            [
                'jadwal_id' => 3,
                'user_id'   => 6,
                'jadwal_nama'  => 'Jadwal 3',
                'jadwal_tgl'  => '2026-01-15',
                'jadwal_jam_mulai'  => '10:00:00',
                'jadwal_jam_selesai'  => '11:00:00',
                'jadwal_jumPes' => 25,
            ],
        ];

        DB::table('t_jadwal')->insert($data);
    }
}
