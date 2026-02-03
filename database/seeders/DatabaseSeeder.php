<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lapangan;
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
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@manfutsal.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'phone' => '08123456789',
            'address' => 'Jakarta, Indonesia',
            'is_active' => true,
        ]);

        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@manfutsal.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '08123456788',
            'address' => 'Jakarta, Indonesia',
            'is_active' => true,
        ]);

        // Create Manager
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@manfutsal.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '08123456787',
            'address' => 'Jakarta, Indonesia',
            'is_active' => true,
        ]);

        // Create sample customers
        User::factory(10)->customer()->create();

        // Create sample lapangan
        Lapangan::factory(15)->create();

        // Create some inactive lapangan for testing
        Lapangan::factory(2)->inactive()->create();

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Super Admin: superadmin@manfutsal.com / password');
        $this->command->info('Admin: admin@manfutsal.com / password');
        $this->command->info('Manager: manager@manfutsal.com / password');
    }
}
