<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@telegateway.io',
            'password' => Hash::make('password'),
            'phone_number' => '+216 71 000 001',
            'gender' => 'male',
            'address' => 'Tunis, Tunisia',
            'last_active_at' => now()->subHours(2),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $operator = User::create([
            'name' => 'Operator User',
            'email' => 'operator@telegateway.io',
            'password' => Hash::make('password'),
            'phone_number' => '+216 71 000 002',
            'gender' => 'female',
            'address' => 'Sousse, Tunisia',
            'last_active_at' => now()->subMinutes(15),
            'is_active' => true,
        ]);
        $operator->assignRole('operator');
    }
}
