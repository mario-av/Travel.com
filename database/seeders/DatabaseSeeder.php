<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Review;
use App\Models\User;
use App\Models\Vacation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Database Seeder - Seeds initial data for the application.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@travel.com',
            'password' => Hash::make('password'),
            'rol' => 'admin',
        ]);

        // Create advanced user
        $advanced = User::factory()->create([
            'name' => 'Advanced User',
            'email' => 'advanced@travel.com',
            'password' => Hash::make('password'),
            'rol' => 'advanced',
        ]);

        // Create regular test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@travel.com',
            'password' => Hash::make('password'),
            'rol' => 'user',
        ]);

        // Create normal users
        $users = User::factory(5)->create();

        // Create categories
        $categories = collect([
            ['name' => 'Beach', 'description' => 'Relaxing beach destinations with sun and sand.'],
            ['name' => 'Mountain', 'description' => 'Adventure in the mountains with hiking and nature.'],
            ['name' => 'City', 'description' => 'Urban exploration and cultural experiences.'],
            ['name' => 'Adventure', 'description' => 'Thrilling adventures and extreme sports.'],
            ['name' => 'Cultural', 'description' => 'Historical sites and cultural immersion.'],
        ])->map(fn($data) => Category::create($data));

        // Create vacations
        $vacations = Vacation::factory(10)
            ->recycle($categories)
            ->recycle($admin)
            ->has(Photo::factory()->count(3))
            ->has(Photo::factory()->main())
            ->create();

        // Create bookings
        Booking::factory(15)
            ->recycle($users->merge([$advanced]))
            ->recycle($vacations)
            ->create();

        // Create reviews
        Review::factory(20)
            ->approved()
            ->recycle($users->merge([$advanced]))
            ->recycle($vacations)
            ->create();
    }
}
