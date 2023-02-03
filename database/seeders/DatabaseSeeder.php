<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()->create(['name' => 'Informática']);
        Category::factory()->create(['name' => 'Electrónica']);
        Category::factory()->create(['name' => 'Videojuegos']);
        Category::factory()->create(['name' => 'Libros']);
        Category::factory()->create(['name' => 'Otros']);

        Product::factory()->count(40)->create();

    }
}
