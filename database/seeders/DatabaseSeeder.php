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
        // Llama a los seeders correctos
        $this->call([
            \Database\Seeders\AdminUserSeeder::class,
            \Database\Seeders\ConsoleSeeder::class,
        ]);
    }
}
