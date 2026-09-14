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
        // 1. SMARTPHONE SAMSUNG GALAXY (5 Unit: HP-TEFA-001 s.d. HP-TEFA-005)
        // =========================================================================
        $samsungUnits = [
            ['num' => '21', 'photo' => 'assets/hp-samsung-#21.jpg'],
            ['num' => '22', 'photo' => 'assets/hp-samsung-#22.jpg'],
            ['num' => '23', 'photo' => 'assets/hp-samsung-#23.jpg'],
            ['num' => '25', 'photo' => 'assets/hp-samsung-#25.jpg'],
            ['num' => '30', 'photo' => 'assets/hp-samsung-#30.jpg'],
        ];

        foreach ($samsungUnits as $idx => $unit) {
            $id = $idx + 1; // 1 s.d. 5
            $codeNumber = str_pad((string) $id, 3, '0', STR_PAD_LEFT);
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

        // =========================================================================
        // 2. LAPTOP ASUS VIVOBOOK 14 (4 Unit: LP-TEFA-006 s.d. LP-TEFA-009)
        // =========================================================================
        $asusUnits = [
            ['num' => '21', 'sn' => 'SN-LP-ASUS-021', 'photo' => 'assets/laptop-asusvivobook-#21.jpg'],
            ['num' => '23', 'sn' => 'SN-LP-ASUS-023', 'photo' => 'assets/laptop-asusvivobook-#23.jpg'],
            ['num' => '24', 'sn' => 'SN-LP-ASUS-024', 'photo' => 'assets/laptop-asusvivobook-#24.jpg'],
            ['num' => '25', 'sn' => 'SN-LP-ASUS-025', 'photo' => 'assets/laptop-asusvivobook-#25.jpg'],
        ];

        foreach ($asusUnits as $idx => $unit) {
            $id = 6 + $idx; // 6 s.d. 9
            $codeNumber = str_pad((string) $id, 3, '0', STR_PAD_LEFT);
            $assets[] = [
                'id' => $id,
                'asset_category_id' => $laptopCat,
                'asset_code' => "LP-TEFA-{$codeNumber}",
                'name' => "Laptop Asus Vivobook #{$unit['num']}",
                'brand' => 'Asus',
                'model' => 'Vivobook 14',
                'serial_number' => $unit['sn'],
                'condition' => AssetCondition::Baik,
                'availability_status' => AssetAvailabilityStatus::Tersedia,
                'photo_path' => $unit['photo'],
                'notes' => 'Core i5-1235U, RAM 16GB, SSD 512GB. Lokasi: Ruang Lab TEFA SMKN 1 Bangsri.',
            ];
        }

        // =========================================================================
        // 3. LAPTOP ACER ASPIRE 3 (23 Unit: LP-TEFA-010 s.d. LP-TEFA-032)
        // 20 unit berfoto + 3 unit tanpa foto (#20, #22, #26)
        // Unit #20 berstatus Tidak Tersedia / Nonaktif (karena hilang)
        // =========================================================================
        $acerUnits = [
            ['label' => '#1',  'sn' => 'SN-LP-ACER-001', 'photo' => 'assets/laptop-acer-#1.jpg'],
            ['label' => '#2',  'sn' => 'SN-LP-ACER-002', 'photo' => 'assets/laptop-acer-#2.jpg'],
            ['label' => '#3',  'sn' => 'SN-LP-ACER-003', 'photo' => 'assets/laptop-acer-#3.jpg'],
            ['label' => '#4',  'sn' => 'SN-LP-ACER-004', 'photo' => 'assets/laptop-acer-#4.jpg'],
            ['label' => '#5',  'sn' => 'SN-LP-ACER-005', 'photo' => 'assets/laptop-acer-#5.jpg'],
            ['label' => '#6',  'sn' => 'SN-LP-ACER-006', 'photo' => 'assets/laptop-acer-#6.jpg'],
            ['label' => '#7',  'sn' => 'SN-LP-ACER-007', 'photo' => 'assets/laptop-acer-#7.jpg'],
            ['label' => '#8',  'sn' => 'SN-LP-ACER-008', 'photo' => 'assets/laptop-acer-#8.jpg'],
            ['label' => '#9',  'sn' => 'SN-LP-ACER-009', 'photo' => 'assets/laptop-acer-#9.jpg'],
            ['label' => '#10', 'sn' => 'SN-LP-ACER-010', 'photo' => 'assets/laptop-acer-#10.jpg'],
            ['label' => '#11', 'sn' => 'SN-LP-ACER-011', 'photo' => 'assets/laptop-acer-#11.jpg'],
            ['label' => '#12', 'sn' => 'SN-LP-ACER-012', 'photo' => 'assets/laptop-acer-#12.jpg'],
            ['label' => '#13', 'sn' => 'SN-LP-ACER-013', 'photo' => 'assets/laptop-acer-#13.jpg'],
            ['label' => '#14', 'sn' => 'SN-LP-ACER-014', 'photo' => 'assets/laptop-acer-#14.jpg'],
            ['label' => '#15', 'sn' => 'SN-LP-ACER-015', 'photo' => 'assets/laptop-acer-#15.jpg'],
            ['label' => '#16', 'sn' => 'SN-LP-ACER-016', 'photo' => 'assets/laptop-acer-#16.jpg'],
            ['label' => '#17', 'sn' => 'SN-LP-ACER-017', 'photo' => 'assets/laptop-acer-#17.jpg'],
            ['label' => '#18', 'sn' => 'SN-LP-ACER-018', 'photo' => 'assets/laptop-acer-#18.jpg'],
            ['label' => '#19', 'sn' => 'SN-LP-ACER-019', 'photo' => 'assets/laptop-acer-#19.jpg'],
            ['label' => '#20', 'sn' => 'SN-LP-ACER-020', 'photo' => null, 'status' => AssetAvailabilityStatus::TidakTersedia, 'notes' => 'Unit dilaporkan hilang / nonaktif. Core i5-1135G7, RAM 8GB, SSD 512GB.'],
            ['label' => '#22', 'sn' => 'SN-LP-ACER-022', 'photo' => null],
            ['label' => '#26', 'sn' => 'SN-LP-ACER-026', 'photo' => null],
            ['label' => '#27', 'sn' => 'SN-LP-ACER-027', 'photo' => 'assets/laptop-acer-#27.jpg'],
        ];

        foreach ($acerUnits as $idx => $unit) {
            $id = 10 + $idx; // 10 s.d. 32
            $codeNumber = str_pad((string) $id, 3, '0', STR_PAD_LEFT);
            $assets[] = [
                'id' => $id,
                'asset_category_id' => $laptopCat,
                'asset_code' => "LP-TEFA-{$codeNumber}",
                'name' => "Laptop Acer Aspire {$unit['label']}",
                'brand' => 'Acer',
                'model' => 'Aspire 3',
                'serial_number' => $unit['sn'],
                'condition' => AssetCondition::Baik,
                'availability_status' => $unit['status'] ?? AssetAvailabilityStatus::Tersedia,
                'photo_path' => $unit['photo'],
                'notes' => $unit['notes'] ?? 'Core i5-1135G7, RAM 8GB, SSD 512GB. Lokasi: Ruang Lab TEFA SMKN 1 Bangsri.',
            ];
        }

        // Hapus aset selain 32 master data TEFA (27 Laptop & 5 HP)
        Asset::where('id', '>', 32)->delete();

        // Hindari tabrakan unique key asset_code & serial_number saat penataan ulang nomor urut ID
        foreach (Asset::all() as $existing) {
            $existing->asset_code = 'TMP-' . $existing->id . '-' . mt_rand(1000, 9999);
            $existing->serial_number = 'TMP-SN-' . $existing->id . '-' . mt_rand(1000, 9999);
            $existing->saveQuietly();
        }

        foreach ($assets as $asset) {
            Asset::updateOrCreate(
                ['id' => $asset['id']],
                $asset
            );
        }
    }
}
