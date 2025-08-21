<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Processador',
        ]);

        Category::create([
            'name' => 'Placa mãe',
        ]);
            

        Category::create([
            'name' => 'Memória RAM',
        ]);

        Category::create([
            'name' => 'Armazenamento',
        ]);

        Category::create([
            'name' => 'Fonte',
        ]);

        Category::create([
            'name' => 'Placa de vídeo',
        ]);

        Category::create([
            'name' => 'Acessórios',
        ]);
    }
}
