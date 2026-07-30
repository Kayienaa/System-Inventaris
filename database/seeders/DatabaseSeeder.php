<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $laptop = Category::create(['nama' => 'Laptop']);
        $hp = Category::create(['nama' => 'Smartphone']);

        // 1. Admin
        User::create([
            'name' => 'Pak Agung (Admin TEFA)',
            'role' => 'admin',
            'email' => 'agung@smkn1bangsri.sch.id',
            'password' => Hash::make('adminTEFA2026'),
        ]);

        // 2. Guru
        User::create([
            'name' => 'Bu Sari (Guru Pembimbing)',
            'role' => 'guru',
            'nip' => '198501012010011001',
            'email' => 'sari@smkn1bangsri.sch.id',
            'password' => Hash::make('guruTEFA2026'),
        ]);

        // 3. Siswa — password = hash(tanggal_lahir), sesuai logic LoginRequest
        $tanggalLahir = '2008-05-12';
        User::create([
            'name' => 'Evan (Siswa PPLG)',
            'role' => 'siswa',
            'nis' => '222310001',
            'email' => 'evan@smkn1bangsri.sch.id',
            'tanggal_lahir' => $tanggalLahir,
            'password' => Hash::make($tanggalLahir),
        ]);

        // 4. Laptop Asus (14 unit) — Ruang TEFA 1
        for ($i = 1; $i <= 14; $i++) {
            Item::create([
                'category_id' => $laptop->id,
                'kode_unik' => 'TEFA-LPT-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_barang' => 'Laptop Asus Vivobook #'.$i,
                'merk' => 'Asus',
                'lokasi_ruangan' => 'Ruang TEFA 1',
                'status' => 'Tersedia',
            ]);
        }

        // 5. Laptop Lenovo (13 unit) — Ruang TEFA 2
        for ($i = 15; $i <= 27; $i++) {
            Item::create([
                'category_id' => $laptop->id,
                'kode_unik' => 'TEFA-LPT-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_barang' => 'Laptop Lenovo ThinkPad #'.$i,
                'merk' => 'Lenovo',
                'lokasi_ruangan' => 'Ruang TEFA 2',
                'status' => 'Tersedia',
            ]);
        }

        // 6. Smartphone (3 unit)
        for ($i = 1; $i <= 3; $i++) {
            Item::create([
                'category_id' => $hp->id,
                'kode_unik' => 'TEFA-HP-00'.$i,
                'nama_barang' => 'Smartphone Pengujian #'.$i,
                'merk' => 'Samsung',
                'lokasi_ruangan' => 'Ruang TEFA 1',
                'status' => 'Tersedia',
            ]);
        }
    }
}