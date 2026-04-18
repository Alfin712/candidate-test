<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\SupplierSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STAFF,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_VIEWER,
                'email_verified_at' => now(),
            ],
        );

        $this->call([
            SupplierSeeder::class,
        ]);
    }
}
