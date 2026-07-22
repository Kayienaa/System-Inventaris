<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin / Petugas TEFA
        User::create([
            'name' => 'Pak Agung (Admin TEFA)',
            'email' => 'agung@smkn1bangsri.sch.id',
            'nis_nip' => '198501012010011001',
            'tanggal_lahir' => '1985-01-01',
            'no_wa' => '081234567890',
            'password' => Hash::make('19850101'), // Password default tgl lahir
        ]);

        // 2. Akun Siswa (Peminjam Dummy)
        User::create([
            'name' => 'Evan (Siswa PPLG)',
            'email' => 'evan@smkn1bangsri.sch.id',
            'nis_nip' => '222310001',
            'tanggal_lahir' => '2008-05-12',
            'no_wa' => '089876543210',
            'password' => Hash::make('20080512'),
        ]);

        // 3. Dummy Data Barang (Hasil Wawancara: Laptop 11 unit, Merchandise, dll)
        
        // 3. Laptop TEFA (Total 27 Unit)
        // 14 Unit Asus di Ruang TEFA 1
        for ($i = 1; $i <= 14; $i++) {
            $kode = 'TEFA-LPT-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            Item::create([
                'kode_unik' => $kode,
                'nama_barang' => 'Laptop Asus Vivobook #' . $i,
                'kategori' => 'Laptop',
                'jenis' => 'Elektronik',
                'merk' => 'Asus',
                'lokasi_ruangan' => 'Ruang TEFA 1',
                'stok' => 1,
                'status' => 'Tersedia',
            ]);
        }

        // 13 Unit Lenovo di Ruang TEFA 2
        for ($i = 15; $i <= 27; $i++) {
            $kode = 'TEFA-LPT-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            Item::create([
                'kode_unik' => $kode,
                'nama_barang' => 'Laptop Lenovo ThinkPad #' . $i,
                'kategori' => 'Laptop',
                'jenis' => 'Elektronik',
                'merk' => 'Lenovo',
                'lokasi_ruangan' => 'Ruang TEFA 2',
                'stok' => 1,
                'status' => 'Tersedia',
            ]);
        }

        // 4. Smartphone / HP (3 Unit)
        for ($i = 1; $i <= 3; $i++) {
            Item::create([
                'kode_unik' => 'TEFA-HP-00' . $i,
                'nama_barang' => 'Smartphone Pengujian #' . $i,
                'kategori' => 'HP',
                'jenis' => 'Elektronik',
                'merk' => 'Samsung',
                'lokasi_ruangan' => 'Ruang TEFA 1',
                'stok' => 1,
                'status' => 'Tersedia',
            ]);
        }

        // Merchandise & Stok Barang
        Item::create([
            'kode_unik' => 'TEFA-MERCH-001',
            'nama_barang' => 'Kaos TEFA PPLG',
            'kategori' => 'Kaos',
            'jenis' => 'Stok',
            'merk' => 'Custom',
            'lokasi_ruangan' => 'Ruang TEFA 1',
            'stok' => 25,
            'status' => 'Tersedia',
        ]);

        Item::create([
            'kode_unik' => 'TEFA-MERCH-002',
            'nama_barang' => 'Tumbler TEFA Stainless',
            'kategori' => 'Tumbler',
            'jenis' => 'Stok',
            'merk' => 'Custom',
            'lokasi_ruangan' => 'Ruang TEFA 1',
            'stok' => 15,
            'status' => 'Tersedia',
        ]);
    }
}