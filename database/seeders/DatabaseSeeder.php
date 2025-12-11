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

        User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
                'name' => 'Seller Account',
                'role' => 'seller',
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'buyer@example.com'],
            [
                'name' => 'Buyer Account',
                'role' => 'buyer',
                'password' => bcrypt('password'),
            ]
        );

        // Call the EcommerceSeeder
        $this->call([
            EcommerceSeeder::class,
        ]);
    }
}
