<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::updateOrCreate(
            ['email' => '3gcomputers1@gmail.com'],
            [
                'name' => '3G Computers Admin',
                'password' => Hash::make('admin5680'),
                'email_verified_at' => now(),
            ]
        );

        $this->call(SearchDemoSeeder::class);
        $this->call(BusinessDemoSeeder::class);
    }
}
