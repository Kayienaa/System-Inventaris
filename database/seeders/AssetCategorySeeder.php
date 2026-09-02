<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'code' => 'CAT-LPT',
                'name' => 'Laptop',
                'description' => 'Perangkat komputer jinjing untuk praktikum, tugas, dan kegiatan operasional TEFA.',
                'is_active' => true,
            ],
            [
                'id' => 2,
                'code' => 'CAT-SMP',
                'name' => 'Smartphone',
                'description' => 'Perangkat telepon pintar untuk pengujian aplikasi mobile, komunikasi, dan multimedia.',
                'is_active' => true,
            ],
            [
                'id' => 3,
                'code' => 'CAT-CAM',
                'name' => 'Kamera',
                'description' => 'Kamera DSLR, mirrorless, dan perlengkapan dokumentasi audio-visual.',
                'is_active' => true,
            ],
            [
                'id' => 4,
                'code' => 'CAT-AV',
                'name' => 'Audio & Visual',
                'description' => 'Proyektor, microphone, speaker, mixer, dan perangkat multimedia lainnya.',
                'is_active' => true,
            ],
            [
                'id' => 5,
                'code' => 'CAT-ACC',
                'name' => 'Aksesoris & Peralatan',
                'description' => 'Kabel konektor, tripod, stabilizer, adapter, dan toolkit pendukung TEFA.',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            AssetCategory::updateOrCreate(
                ['id' => $category['id']],
                [
                    'code' => $category['code'],
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => $category['is_active'],
                ]
            );
        }
    }
}
