<?php

namespace Database\Seeders;

use App\Models\GuruProfile;
use App\Models\SiswaProfile;
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
        $admin = User::updateOrCreate(
            ['email' => 'AdminInventaris@gmail.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Akun Contoh Guru Pengajar
        $guru = User::updateOrCreate(
            ['email' => 'guru@smkn1bangsri.sch.id'],
            [
                'name' => 'Budi Santoso, S.Kom',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $guru->syncRoles(['guru']);
        GuruProfile::updateOrCreate(
            ['user_id' => $guru->id],
            [
                'nip' => '198501012010011001',
                'phone' => '081234567890',
            ]
        );

        // 3. Akun Contoh Siswa TEFA
        $siswa = User::updateOrCreate(
            ['email' => '1234@smkn1bangsri.sch.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $siswa->syncRoles(['siswa']);
        SiswaProfile::updateOrCreate(
            ['user_id' => $siswa->id],
            [
                'nis' => '1234',
                'nisn' => '0051234567',
                'class_name' => 'XII RPL 1',
                'phone' => '081234567891',
            ]
        );
    }
}


