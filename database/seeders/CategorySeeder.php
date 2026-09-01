<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'nama' => 'Laptop',
                'deskripsi' => 'Perangkat komputer jinjing untuk praktikum, tugas, dan kegiatan operasional TEFA.',
            ],
            [
                'id' => 2,
                'nama' => 'Smartphone',
                'deskripsi' => 'Perangkat telepon pintar untuk pengujian aplikasi mobile, komunikasi, dan multimedia.',
            ],
            [
                'id' => 3,
                'nama' => 'Kamera',
                'deskripsi' => 'Kamera DSLR, mirrorless, dan perlengkapan dokumentasi audio-visual.',
            ],
            [
                'id' => 4,
                'nama' => 'Audio & Visual',
                'deskripsi' => 'Proyektor, microphone, speaker, mixer, dan perangkat multimedia lainnya.',
            ],
            [
                'id' => 5,
                'nama' => 'Aksesoris & Peralatan',
                'deskripsi' => 'Kabel konektor, tripod, stabilizer, adapter, dan toolkit pendukung TEFA.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                [
                    'nama' => $category['nama'],
                    'deskripsi' => $category['deskripsi'],
                ]
            );
        }
    }
}
