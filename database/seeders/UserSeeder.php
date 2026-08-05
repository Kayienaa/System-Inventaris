<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Inventaris',
            'email' => 'admin@inventaris.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'identitas' => 'ADMIN001',
            'nomor_whatsapp' => '081234567890',
            'foto_profil' => null,
            'email_verified_at' => now(),
        ]);

        // User
        User::create([
            'name' => 'Karin',
            'email' => 'karin@gmail.com',
            'password' => Hash::make('karin123'),
            'role' => 'user',
            'identitas' => '123456789',
            'nomor_whatsapp' => '081298765432',
            'foto_profil' => null,
            'email_verified_at' => now(),
        ]);
    }
}