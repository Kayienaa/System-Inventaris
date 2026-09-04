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

        $assets = [];

        // ==========================================
        // --- 27 LAPTOPS (LP-TEFA-001 s.d. LP-TEFA-027) ---
        // ==========================================
        // 14 Unit Asus Vivobook (LP-TEFA-001 s.d. LP-TEFA-014)
        // 13 Unit Acer Aspire (LP-TEFA-015 s.d. LP-TEFA-027)
        for ($i = 1; $i <= 27; $i++) {
            $codeNumber = str_pad((string) $i, 3, '0', STR_PAD_LEFT);
            $assetCode = "LP-TEFA-{$codeNumber}";
            $photo = $availableLaptopPhotos[($i - 1) % count($availableLaptopPhotos)];

            if ($i <= 14) {
                // Asus Vivobook (14 unit)
                $name = 'Laptop Asus Vivobook';
                $brand = 'Asus';
                $model = 'Vivobook 14';
                $serialNumber = "SN-LP-ASUS-{$codeNumber}";
                $notes = 'Core i5-1235U, RAM 16GB, SSD 512GB. Lokasi: Ruang Lab TEFA SMKN 1 Bangsri.';
            } else {
                // Acer Aspire (13 unit)
                $name = 'Laptop Acer Aspire';
                $brand = 'Acer';
                $model = 'Aspire 3';
                $serialNumber = "SN-LP-ACER-{$codeNumber}";
                $notes = 'Core i5-1135G7, RAM 8GB, SSD 512GB. Lokasi: Ruang Lab TEFA SMKN 1 Bangsri.';
            }

            $assets[] = [
                'id' => $i,
                'asset_category_id' => $laptopCat,
                'asset_code' => $assetCode,
                'name' => $name,
                'brand' => $brand,
                'model' => $model,
                'serial_number' => $serialNumber,
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $photo,
                'notes' => $notes,
            ];
        }

        // ==========================================
        // --- 3 HP / SMARTPHONES (HP-TEFA-001 s.d. HP-TEFA-003) ---
        // ==========================================
        $hpPhotos = [
            'items/hp-21.jpg',
            'items/hp-22.jpg',
            'items/hp-25.jpg',
        ];

        for ($j = 1; $j <= 3; $j++) {
            $codeNumber = str_pad((string) $j, 3, '0', STR_PAD_LEFT);
            $assets[] = [
                'id' => 27 + $j,
                'asset_category_id' => $hpCat,
                'asset_code' => "HP-TEFA-{$codeNumber}",
                'name' => 'HP Samsung Galaxy',
                'brand' => 'Samsung',
                'model' => 'Galaxy A54 5G',
                'serial_number' => "SN-HP-SMSG-{$codeNumber}",
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $hpPhotos[($j - 1) % count($hpPhotos)],
                'notes' => 'Samsung Galaxy A54 5G. RAM 8GB, Storage 256GB. Testing unit mobile TEFA.',
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
