<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SekolahSeeder::class,
            AdminSeeder::class,
            GuruSeeder::class,
            MapelSeeder::class,
        ]);
    }
}
