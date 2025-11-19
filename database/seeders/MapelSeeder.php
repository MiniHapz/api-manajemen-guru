<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelSeeder extends Seeder
{
    public function run(): void
    {
        $mapels = [
            'Pendidikan Pancasila & Kewarganegaraan (PPKn)',
            'Bahasa Indonesia',
            'Bahasa Inggris',
            'Matematika',
            'Seni Budaya',
            'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)',
        ];

        // Insert mapel master
        $insertMapel = [];
        foreach ($mapels as $mapel) {
            $insertMapel[] = [
                'nama_mapel' => $mapel,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('mapel')->insert($insertMapel);

        // Ambil id mapel
        $mapelIds = DB::table('mapel')->pluck('id');

        // Ambil semua guru + sekolahnya
        $guruList = DB::table('guru')->select('nip', 'sekolah_id')->get();

        foreach ($guruList as $guru) {
            DB::table('guru_mapel')->insert([
                'guru_nip'   => $guru->nip,
                'mapel_id'   => $mapelIds->random(),
                'sekolah_id' => $guru->sekolah_id, // <-- auto sesuai sekolah guru
                'custom'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
