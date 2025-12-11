<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Seller Account',
            'email' => 'seller@example.com',
            'role' => 'seller',
            'password' => 'password',
        ]);

        User::factory()->create([
            'name' => 'Buyer Account',
            'email' => 'buyer@example.com',
            'role' => 'buyer',
            'password' => 'password',
        ]);

        $defaultCategories = [
            ['category_name' => 'Mobile Phones', 'description' => 'Smartphones and mobile devices'],
            ['category_name' => 'Laptop', 'description' => 'Notebooks and ultrabooks'],
            ['category_name' => 'Smart Watch', 'description' => 'Wearables and smartwatches'],
            ['category_name' => 'Earbuds', 'description' => 'Wireless and wired earbuds'],
        ];

        foreach ($defaultCategories as $category) {
            \App\Models\Category::firstOrCreate(
                ['category_name' => $category['category_name']],
                ['description' => $category['description']]
            );
        }
    }
}
