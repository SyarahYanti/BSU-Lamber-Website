<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat akun admin default
        User::create([
            'name' => 'Admin',
            'email' => 'syarahyanti013@gmail.com',
            'password' => Hash::make('syarah123'), // password untuk login
        ]);
    }
}
