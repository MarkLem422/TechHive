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
        $categories = [
            [
                'category_name' => 'Mobile Phones',
                'description' => 'Smartphones of all brands and models.',
            ],
            [
                'category_name' => 'Laptop',
                'description' => 'Laptops, notebooks, and ultrabooks for work and play.',
            ],
            [
                'category_name' => 'Smart Watch',
                'description' => 'Wearable smart devices to track health and notifications.',
            ],
            [
                'category_name' => 'Earbuds',
                'description' => 'Wireless and wired earbuds for music and calls.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
