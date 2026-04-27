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
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $operator = User::create([
            'name' => 'Operator User',
            'email' => 'operator@telegateway.io',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $operator->assignRole('operator');
    }
}
