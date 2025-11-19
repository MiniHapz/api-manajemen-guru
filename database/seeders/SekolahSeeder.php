<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SekolahSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sekolah')->insert([
            [
                'nama_sekolah' => 'SMA Negeri 3 Bandung',
                'jenjang'     => 'SMA',
                'akreditasi'  => 'A',
                'npsn'        => '69927506',
                'alamat'      => 'Jl. Asia Afrika No. 45, Bandung',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nama_sekolah' => 'SMK Negeri 2 Surakarta',
                'jenjang'     => 'SMK',
                'akreditasi'  => 'B',
                'npsn'        => '20500987',
                'alamat'      => 'Jl. Pahlawan No. 12, Surakarta',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'nama_sekolah' => 'SLB Negeri 1 Malang',
                'jenjang'     => 'SLB',
                'akreditasi'  => 'A',
                'npsn'        => '20212115',
                'alamat'      => 'Jl. Merdeka No.1, Malang',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
