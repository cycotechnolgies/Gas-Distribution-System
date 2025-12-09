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

    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gas.com',
            'password' => Hash::make('admin@1234'),
            'role' => 'admin'
        ]);
    }

}
