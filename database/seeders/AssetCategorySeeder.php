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
                'code' => 'CAT-HP',
                'name' => 'HP',
                'description' => 'Perangkat telepon pintar (smartphone) untuk pengujian aplikasi mobile, komunikasi, dan multimedia.',
                'is_active' => true,
            ],
        ];

        // Hapus kategori di luar Laptop dan HP
        AssetCategory::whereNotIn('id', [1, 2])
            ->whereNotIn('code', ['CAT-LPT', 'CAT-HP'])
            ->delete();

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
