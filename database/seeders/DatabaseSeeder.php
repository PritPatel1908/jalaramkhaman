<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'fname' => 'super',
            'lname' => 'admin',
            'name' => 'superadmin',
            'email' => 'admin@gmail.com',
            'password' => 'Indian@123',
            'user_type' => 'admin',
            'is_locked' => false,
            'gender' => 'male',
            'is_activate' => true,
        ]);

        User::factory()->create([
            'fname' => 'prit',
            'lname' => 'patel',
            'name' => 'prit patel',
            'email' => 'prit89039@gmail.com',
            'password' => 'Prit@1908',
            'user_type' => 'business',
            'is_locked' => false,
            'gender' => 'male',
            'is_activate' => true,
        ]);

        User::factory()->create([
            'fname' => 'prit',
            'lname' => 'patel',
            'name' => 'prit patel',
            'email' => 'prit1908@gmail.com',
            'password' => 'Prit@1908',
            'user_type' => 'customer',
            'is_locked' => false,
            'gender' => 'male',
            'is_activate' => true,
        ]);

        $this->call([
            DefaultSeeder::class
        ]);
    }
}
