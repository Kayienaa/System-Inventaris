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
        $admin = User::firstOrCreate(
            ['email' => 'admin@inventaris.com'],
            [
                'name' => 'Admin Inventaris',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Guru
        $guru = User::firstOrCreate(
            ['email' => 'guru@inventaris.com'],
            [
                'name' => 'Guru Contoh',
                'password' => Hash::make('guru123'),
                'email_verified_at' => now(),
            ]
        );
        $guru->assignRole('guru');

        // Siswa
        $siswa = User::firstOrCreate(
            ['email' => 'karin@gmail.com'],
            [
                'name' => 'Karin',
                'password' => Hash::make('karin123'),
                'email_verified_at' => now(),
            ]
        );
        $siswa->assignRole('siswa');
    }
}
