<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $seedUsers = [
            ['email' => 'test@example.com',   'name' => 'Test User',   'role' => 'admin'],
            ['email' => 'staff@example.com',  'name' => 'Staff User',  'role' => 'staff'],
            ['email' => 'viewer@example.com', 'name' => 'Viewer User', 'role' => 'viewer'],
        ];

        foreach ($seedUsers as $user) {
            $exists = DB::table('users')->where('email', $user['email'])->exists();

            if ($exists) {
                DB::table('users')
                    ->where('email', $user['email'])
                    ->update([
                        'role' => $user['role'],
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('users')->insert([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => Hash::make('password'),
                    'role' => $user['role'],
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // One-way role fix — no rollback.
    }
};
