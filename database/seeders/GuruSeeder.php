<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil 3 sekolah pertama
        $sekolah = DB::table('sekolah')->take(3)->get();

        if ($sekolah->count() < 3) {
            throw new \Exception("Minimal harus ada 3 sekolah sebelum menjalankan GuruSeeder.");
        }

        // ============================
        // DATA GURU SEKOLAH 1
        // ============================
        $guruSekolah1 = [
            [
                'nip' => '197901012023110001',
                'nama_lengkap' => 'Dedi Priyanto',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1979-01-01',
                'jenis_kelamin' => 'L',
                'status_kepegawaian' => 'pns',
                'pendidikan_terakhir' => 'S1 Pendidikan Fisika',
                'telepon' => '081200100200',
                'email' => 'dedi.priyanto@example.com',
                'alamat' => 'Jl. Kenanga No.10, Surabaya',
                'tanggal_bergabung' => '2008-07-15',
                'tanggal_pensiun' => '2044-02-01',
                'jam_mengajar_per_minggu' => '11',
            ],
            [
                'nip' => '198603052023110002',
                'nama_lengkap' => 'Ayu Rahmadani',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1986-03-05',
                'jenis_kelamin' => 'P',
                'status_kepegawaian' => 'p3k',
                'pendidikan_terakhir' => 'S1 Pendidikan Bahasa Inggris',
                'telepon' => '082233445566',
                'email' => 'ayu.rahmadani@example.com',
                'alamat' => 'Jl. Kamboja No.22, Bandung',
                'tanggal_bergabung' => '2014-02-10',
                'tanggal_pensiun' => '2051-04-05',
                'jam_mengajar_per_minggu' => '21',
            ],
            [
                'nip' => '199112202023110003',
                'nama_lengkap' => 'Fajar Saputra',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1991-12-20',
                'jenis_kelamin' => 'L',
                'status_kepegawaian' => 'p3k_paruh_waktu',
                'pendidikan_terakhir' => 'S1 Ekonomi',
                'telepon' => '081377889900',
                'email' => 'fajar.saputra@example.com',
                'alamat' => 'Jl. Mawar No.9, Semarang',
                'tanggal_bergabung' => '2018-09-21',
                'tanggal_pensiun' => '2057-1-20',
                'jam_mengajar_per_minggu' => '24',
            ],
        ];

        // ============================
        // DATA GURU SEKOLAH 2
        // ============================
        $guruSekolah2 = [
            [
                'nip' => '198002202023110011',
                'nama_lengkap' => 'Rina Marlina',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1980-02-20',
                'jenis_kelamin' => 'P',
                'status_kepegawaian' => 'pns',
                'pendidikan_terakhir' => 'S1 Kimia',
                'telepon' => '081244556677',
                'email' => 'rina.marlina@example.com',
                'alamat' => 'Jl. Flamboyan No.5, Jakarta',
                'tanggal_bergabung' => '2009-08-11',
                'tanggal_pensiun' => '2045-03-20',
                'jam_mengajar_per_minggu' => '18',
            ],
            [
                'nip' => '198911152023110012',
                'nama_lengkap' => 'Bagus Pratama',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1989-11-15',
                'jenis_kelamin' => 'L',
                'status_kepegawaian' => 'p3k',
                'pendidikan_terakhir' => 'S1 Geografi',
                'telepon' => '082144556677',
                'email' => 'bagus.pratama@example.com',
                'alamat' => 'Jl. Mawar No.7, Yogyakarta',
                'tanggal_bergabung' => '2016-01-22',
                'tanggal_pensiun' => '2054-12-15',
                'jam_mengajar_per_minggu' => '16',
            ],
            [
                'nip' => '199305102023110013',
                'nama_lengkap' => 'Intan Lestari',
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => '1993-05-10',
                'jenis_kelamin' => 'P',
                'status_kepegawaian' => 'p3k_paruh_waktu',
                'pendidikan_terakhir' => 'S1 Pendidikan Seni',
                'telepon' => '083877665544',
                'email' => 'intan.lestari@example.com',
                'alamat' => 'Jl. Tanjung No.3, Malang',
                'tanggal_bergabung' => '2019-04-17',
                'tanggal_pensiun' => '2058-06-10',
                'jam_mengajar_per_minggu' => '15',
            ],
        ];

        // ============================
        // DATA GURU SEKOLAH 3 (1 orang)
        // ============================
        $guruSekolah3 = [
            [
                'nip' => '198705102023110021',
                'nama_lengkap' => 'Sulastri Widyaningsih',
                'tempat_lahir' => 'Kediri',
                'tanggal_lahir' => '1987-05-10',
                'jenis_kelamin' => 'P',
                'status_kepegawaian' => 'pns',
                'pendidikan_terakhir' => 'S1 Pendidikan Matematika',
                'telepon' => '082199887766',
                'email' => 'sulastri.widya@example.com',
                'alamat' => 'Jl. Melati No.4, Kediri',
                'tanggal_bergabung' => '2012-03-01',
                'tanggal_pensiun' => '2047-06-10',
                'jam_mengajar_per_minggu' => '12',
            ],
        ];

        // SIAPKAN INSERT
        $insert = [];

        foreach ($guruSekolah1 as $g) {
            $insert[] = array_merge($g, [
                'sekolah_id' => $sekolah[0]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($guruSekolah2 as $g) {
            $insert[] = array_merge($g, [
                'sekolah_id' => $sekolah[1]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($guruSekolah3 as $g) {
            $insert[] = array_merge($g, [
                'sekolah_id' => $sekolah[2]->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('guru')->insert($insert);
    }
}
