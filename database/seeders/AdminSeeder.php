<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ===== Admin =====
        DB::table('users')->insert([
            'name' => 'Kuasa Admin',
            'username' => 'admin', // ganti dari email ke username
            'password' => Hash::make('12345678'), // password default
            'role' => 'admin',
            'sekolah_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ===== User biasa =====
        $sekolah = DB::table('sekolah')->first(); // ambil sekolah pertama sebagai default jika ada
        DB::table('users')->insert([
            'name' => 'Guru Sigma',
            'username' => 'user', // username unik
            'password' => Hash::make('12345678'),
            'role' => 'user',
            'sekolah_id' => $sekolah ? $sekolah->id : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
