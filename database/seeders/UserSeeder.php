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
        // 1. Super Administrator Resmi TEFA
        $superAdmin = User::updateOrCreate(
            ['email' => 'AdminInventaris@gmail.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['super_admin']);

        // 2. Akun Operasional Resmi Admin TEFA (Mas Donny)
        $adminDonny = User::updateOrCreate(
            ['email' => 'admindonny@gmail.com'],
            [
                'name' => 'Mas Donny (Admin TEFA)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminDonny->syncRoles(['admin']);
    }
}


