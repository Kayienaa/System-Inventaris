<?php

namespace Database\Seeders;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\AssetCondition;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $laptopCat = AssetCategory::where('code', 'CAT-LPT')->orWhere('name', 'Laptop')->first()?->id ?? 1;
        $hpCat = AssetCategory::where('code', 'CAT-HP')->orWhere('name', 'HP')->first()?->id ?? 2;

        $availableLaptopPhotos = [
            'items/laptop-2.jpg',
            'items/laptop-3.jpg',
            'items/laptop-4.jpg',
            'items/laptop-5.jpg',
            'items/laptop-6.jpg',
            'items/laptop-8.jpg',
            'items/laptop-10.jpg',
            'items/laptop-12.jpg',
            'items/laptop-13.jpg',
            'items/laptop-14.jpg',
            'items/laptop-16.jpg',
            'items/lapotp-19.jpg',
            'items/laptop-21.jpg',
            'items/laptop-25.jpg',
            'items/laptop-27.jpg',
            'items/laptop-noname.jpg',
        ];

        $laptopBrands = [
            ['brand' => 'Asus', 'model' => 'Vivobook 14 A1404', 'specs' => 'Core i5-1235U, RAM 16GB, SSD 512GB'],
            ['brand' => 'Lenovo', 'model' => 'ThinkPad L14 Gen 2', 'specs' => 'Ryzen 5 PRO, RAM 16GB, SSD 512GB'],
            ['brand' => 'Acer', 'model' => 'Aspire 5 A514', 'specs' => 'Core i5-1135G7, RAM 16GB, SSD 512GB'],
            ['brand' => 'HP', 'model' => 'Pavilion 14-dv2000', 'specs' => 'Core i5-1235U, RAM 16GB, SSD 512GB'],
            ['brand' => 'Dell', 'model' => 'Latitude 3420', 'specs' => 'Core i5-1135G7, RAM 16GB, SSD 512GB'],
            ['brand' => 'Asus', 'model' => 'ExpertBook B1400', 'specs' => 'Core i5-1135G7, RAM 16GB, SSD 512GB'],
            ['brand' => 'Lenovo', 'model' => 'IdeaPad 3 14ITL6', 'specs' => 'Ryzen 5 5500U, RAM 8GB, SSD 512GB'],
            ['brand' => 'Acer', 'model' => 'Swift 3 SF314', 'specs' => 'Core i5-1135G7, RAM 8GB, SSD 512GB'],
            ['brand' => 'HP', 'model' => 'ProBook 440 G8', 'specs' => 'Core i5-1135G7, RAM 16GB, SSD 512GB'],
        ];

        $assets = [];

        // ==========================================
        // --- 27 LAPTOPS (LP-TEFA-001 s.d. LP-TEFA-027) ---
        // ==========================================
        for ($i = 1; $i <= 27; $i++) {
            $codeNumber = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $assetCode = "LP-TEFA-{$codeNumber}";
            $serialNumber = "SN-LP-TEFA-{$codeNumber}";
            $specIndex = ($i - 1) % count($laptopBrands);
            $brandInfo = $laptopBrands[$specIndex];
            $photo = $availableLaptopPhotos[($i - 1) % count($availableLaptopPhotos)];

            $assets[] = [
                'id' => $i,
                'asset_category_id' => $laptopCat,
                'asset_code' => $assetCode,
                'name' => "Laptop {$brandInfo['brand']} {$brandInfo['model']} #{$i}",
                'brand' => $brandInfo['brand'],
                'model' => $brandInfo['model'],
                'serial_number' => $serialNumber,
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $photo,
                'notes' => "{$brandInfo['specs']}. Lokasi: Ruang Lab TEFA SMKN 1 Bangsri.",
            ];
        }

        // ==========================================
        // --- 3 HP / SMARTPHONES (HP-TEFA-001 s.d. HP-TEFA-003) ---
        // ==========================================
        $hpItems = [
            [
                'id' => 28,
                'code' => 'HP-TEFA-001',
                'name' => 'HP Samsung Galaxy A54 5G #1',
                'brand' => 'Samsung',
                'model' => 'Galaxy A54 5G',
                'serial' => 'SN-HP-TEFA-001',
                'photo' => 'items/hp-21.jpg',
                'notes' => 'RAM 8GB, Storage 256GB. Testing unit mobile TEFA.',
            ],
            [
                'id' => 29,
                'code' => 'HP-TEFA-002',
                'name' => 'HP Xiaomi Redmi Note 12 #2',
                'brand' => 'Xiaomi',
                'model' => 'Redmi Note 12',
                'serial' => 'SN-HP-TEFA-002',
                'photo' => 'items/hp-25.jpg',
                'notes' => 'RAM 8GB, Storage 128GB. Testing unit mobile TEFA.',
            ],
            [
                'id' => 30,
                'code' => 'HP-TEFA-003',
                'name' => 'HP Apple iPhone 11 #3',
                'brand' => 'Apple',
                'model' => 'iPhone 11 64GB',
                'serial' => 'SN-HP-TEFA-003',
                'photo' => 'items/hp-30.jpg',
                'notes' => 'iOS testing unit untuk multimedia dan mobile dev.',
            ],
        ];

        foreach ($hpItems as $hp) {
            $assets[] = [
                'id' => $hp['id'],
                'asset_category_id' => $hpCat,
                'asset_code' => $hp['code'],
                'name' => $hp['name'],
                'brand' => $hp['brand'],
                'model' => $hp['model'],
                'serial_number' => $hp['serial'],
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $hp['photo'],
                'notes' => $hp['notes'],
            ];
        }

        // Hapus aset selain 30 master data TEFA (27 Laptop & 3 HP)
        Asset::where('id', '>', 30)->delete();

        foreach ($assets as $asset) {
            Asset::updateOrCreate(
                ['id' => $asset['id']],
                $asset
            );
        }
    }
}
