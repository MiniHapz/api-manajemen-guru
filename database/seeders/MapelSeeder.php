<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        // Matikan FK dulu biar bebas truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('guru_mapel')->truncate();
        DB::table('mapel')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $mapels = [
            'Pendidikan Pancasila & Kewarganegaraan (PPKn)',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Matematika',
            'Seni Budaya',
            'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)',
        ];

        // Insert master mapel
        $insertMapel = [];
        foreach ($mapels as $mapel) {
            $insertMapel[] = [
                'nama_mapel' => $mapel,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('mapel')->insert($insertMapel);

        // Ambil semua mapel id
        $mapelIds = DB::table('mapel')->pluck('id');

        // Ambil semua guru + sekolahnya
        $guruList = DB::table('guru')->select('nip', 'sekolah_id')->get();

        // Assign mapel random ke tiap guru
        foreach ($guruList as $guru) {
            DB::table('guru_mapel')->insert([
                'guru_nip'   => $guru->nip,
                'mapel_id'   => $mapelIds->random(),
                'sekolah_id' => $guru->sekolah_id,
                'custom'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
