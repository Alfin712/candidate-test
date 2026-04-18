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
            ],
        );

        User::updateOrCreate(
            ['email' => 'engineer@example.com'],
            [
                'name' => 'Engineer User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ENGINEER,
            ],
        );

        User::updateOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_VIEWER,
            ],
        );

        $this->call([
            SupplierSeeder::class,
        ]);
    }
}
