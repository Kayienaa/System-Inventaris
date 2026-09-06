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

        $assets = [];

        // =========================================================================
        // 1. LAPTOP ASUS VIVOBOOK (13 Unit: LP-TEFA-001 s.d. LP-TEFA-013)
        // Foto tersedia: #21, #25
        // Unit lainnya (seperti #24 dan nomor lainnya yang fotonya belum ada): null
        // =========================================================================
        $asusUnits = [
            ['num' => '01', 'photo' => null],
            ['num' => '02', 'photo' => null],
            ['num' => '03', 'photo' => null],
            ['num' => '04', 'photo' => null],
            ['num' => '05', 'photo' => null],
            ['num' => '06', 'photo' => null],
            ['num' => '07', 'photo' => null],
            ['num' => '08', 'photo' => null],
            ['num' => '09', 'photo' => null],
            ['num' => '10', 'photo' => null],
            ['num' => '21', 'photo' => 'assets/laptop-asusvivobook-#21.jpg'],
            ['num' => '24', 'photo' => null],
            ['num' => '25', 'photo' => 'assets/laptop-asusvivobook-#25.jpg'],
        ];

        foreach ($asusUnits as $idx => $unit) {
            $id = $idx + 1; // 1 s.d. 13
            $codeNumber = str_pad((string) $id, 3, '0', STR_PAD_LEFT);
            $assets[] = [
                'id' => $id,
                'asset_category_id' => $laptopCat,
                'asset_code' => "LP-TEFA-{$codeNumber}",
                'name' => "Laptop Asus Vivobook #{$unit['num']}",
                'brand' => 'Asus',
                'model' => 'Vivobook 14',
                'serial_number' => "SN-LP-ASUS-{$codeNumber}",
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $unit['photo'],
                'notes' => 'Core i5-1235U, RAM 16GB, SSD 512GB. Lokasi: Ruang Lab TEFA SMKN 1 Bangsri.',
            ];
        }

        // =========================================================================
        // 2. LAPTOP ACER ASPIRE (14 Unit: LP-TEFA-014 s.d. LP-TEFA-027)
        // Foto tersedia: #2, #3, #4, #5, #6, #8, #10, #12, #13, #14, #16, #19, #27, nokodeunik
        // =========================================================================
        $acerUnits = [
            ['label' => '#2', 'photo' => 'assets/laptop-acer-#2.jpg'],
            ['label' => '#3', 'photo' => 'assets/laptop-acer-#3.jpg'],
            ['label' => '#4', 'photo' => 'assets/laptop-acer-#4.jpg'],
            ['label' => '#5', 'photo' => 'assets/laptop-acer-#5.jpg'],
            ['label' => '#6', 'photo' => 'assets/laptop-acer-#6.jpg'],
            ['label' => '#8', 'photo' => 'assets/laptop-acer-#8.jpg'],
            ['label' => '#10', 'photo' => 'assets/laptop-acer-#10.jpg'],
            ['label' => '#12', 'photo' => 'assets/laptop-acer-#12.jpg'],
            ['label' => '#13', 'photo' => 'assets/laptop-acer-#13.jpg'],
            ['label' => '#14', 'photo' => 'assets/laptop-acer-#14.jpg'],
            ['label' => '#16', 'photo' => 'assets/laptop-acer-#16.jpg'],
            ['label' => '#19', 'photo' => 'assets/laptop-acer-#19.jpg'],
            ['label' => '#27', 'photo' => 'assets/laptop-acer-#27.jpg'],
            ['label' => '(No Kode Unik)', 'photo' => 'assets/laptop-acer-nokodeunik.jpg'],
        ];

        foreach ($acerUnits as $idx => $unit) {
            $id = 14 + $idx; // 14 s.d. 27
            $codeNumber = str_pad((string) $id, 3, '0', STR_PAD_LEFT);
            $assets[] = [
                'id' => $id,
                'asset_category_id' => $laptopCat,
                'asset_code' => "LP-TEFA-{$codeNumber}",
                'name' => "Laptop Acer Aspire {$unit['label']}",
                'brand' => 'Acer',
                'model' => 'Aspire 3',
                'serial_number' => "SN-LP-ACER-{$codeNumber}",
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $unit['photo'],
                'notes' => 'Core i5-1135G7, RAM 8GB, SSD 512GB. Lokasi: Ruang Lab TEFA SMKN 1 Bangsri.',
            ];
        }

        // =========================================================================
        // 3. SMARTPHONE SAMSUNG GALAXY (3 Unit: HP-TEFA-001 s.d. HP-TEFA-003)
        // Foto tersedia: #21, #22, #25 (dan #30)
        // =========================================================================
        $samsungUnits = [
            ['num' => '21', 'photo' => 'assets/hp-samsung-#21.jpg'],
            ['num' => '22', 'photo' => 'assets/hp-samsung-#22.jpg'],
            ['num' => '25', 'photo' => 'assets/hp-samsung-#25.jpg'],
        ];

        foreach ($samsungUnits as $idx => $unit) {
            $id = 28 + $idx; // 28 s.d. 30
            $codeNumber = str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT);
            $assets[] = [
                'id' => $id,
                'asset_category_id' => $hpCat,
                'asset_code' => "HP-TEFA-{$codeNumber}",
                'name' => "HP Samsung Galaxy A54 5G #{$unit['num']}",
                'brand' => 'Samsung',
                'model' => 'Galaxy A54 5G',
                'serial_number' => "SN-HP-SMSG-{$codeNumber}",
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $unit['photo'],
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
