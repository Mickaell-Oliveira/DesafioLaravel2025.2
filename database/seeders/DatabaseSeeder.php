<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class, // Seeder recomendada para testes
        ]);
    }
}