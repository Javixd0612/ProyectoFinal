<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Llama a los seeders que necesites
        $this->call([
            AdminUserSeeder::class,
            ConsolasTarifasSeeder::class,
        ]);
    }
}
