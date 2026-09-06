<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Services\ImageCompressionService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BulkUploadAssetsPhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tefa:bulk-upload-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-upload dan auto-compress seluruh berkas foto fisik aset di storage/app/public/items langsung ke tabel assets';

    /**
     * Pemetaan nama berkas fisik ke kode aset unik inventaris.
     *
     * @var array<string, string>
     */
    protected array $photoMappings = [
        // 1. Smartphone Samsung Galaxy
        'hp-samsung-#21.jpg' => 'HP-TEFA-001',
        'hp-samsung-#22.jpg' => 'HP-TEFA-002',
        'hp-samsung-#25.jpg' => 'HP-TEFA-003',

        // 2. Laptop Asus Vivobook
        'laptop-asusvivobook-#21.jpg' => 'LP-TEFA-011',
        'laptop-asusvivobook-#25.jpg' => 'LP-TEFA-013',

        // 3. Laptop Acer Aspire
        'laptop-acer-#2.jpg' => 'LP-TEFA-014',
        'laptop-acer-#3.jpg' => 'LP-TEFA-015',
        'laptop-acer-#4.jpg' => 'LP-TEFA-016',
        'laptop-acer-#5.jpg' => 'LP-TEFA-017',
        'laptop-acer-#6.jpg' => 'LP-TEFA-018',
        'laptop-acer-#8.jpg' => 'LP-TEFA-019',
        'laptop-acer-#10.jpg' => 'LP-TEFA-020',
        'laptop-acer-#12.jpg' => 'LP-TEFA-021',
        'laptop-acer-#13.jpg' => 'LP-TEFA-022',
        'laptop-acer-#14.jpg' => 'LP-TEFA-023',
        'laptop-acer-#16.jpg' => 'LP-TEFA-024',
        'laptop-acer-#19.jpg' => 'LP-TEFA-025',
        'laptop-acer-#27.jpg' => 'LP-TEFA-026',
        'laptop-acer-nokodeunik.jpg' => 'LP-TEFA-027',
    ];

    /**
     * Execute the console command.
     */
    public function handle(ImageCompressionService $compressor): int
    {
        $itemsDir = storage_path('app/public/items');

        $this->info('====================================================');
        $this->info('  TE-VAULT BULK ASSET PHOTO UPLOADER & COMPRESSOR  ');
        $this->info('====================================================');

        // 1. Periksa ketersediaan direktori items
        if (!is_dir($itemsDir)) {
            $this->error("Direktori sumber [{$itemsDir}] tidak ditemukan.");
            return self::FAILURE;
        }

        $this->line("Direktori sumber: {$itemsDir}");
        $this->line("Memulai proses upload & kompresi foto fisik aset..." . PHP_EOL);

        $processedCount = 0;
        $failedCount = 0;

        // Pastikan folder penyimpanan assets di public disk siap
        if (!Storage::disk('public')->exists('assets')) {
            Storage::disk('public')->makeDirectory('assets');
        }

        foreach ($this->photoMappings as $filename => $assetCode) {
            $fullPath = $itemsDir . DIRECTORY_SEPARATOR . $filename;

            // 2. Validasi ketersediaan file fisik
            if (!file_exists($fullPath)) {
                $this->warn("[-] Berkas [{$filename}] tidak ditemukan di direktori items. Melewati...");
                $failedCount++;
                continue;
            }

            // 3. Validasi entitas aset di database
            $asset = Asset::where('asset_code', $assetCode)->first();
            if (!$asset) {
                $this->warn("[-] Aset dengan kode [{$assetCode}] tidak ditemukan di database. Melewati...");
                $failedCount++;
                continue;
            }

            try {
                $initialSizeKb = round(filesize($fullPath) / 1024, 2);

                // 4. Konversi file lokal menjadi instance UploadedFile (test mode aktif)
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                $uploadedFile = new UploadedFile(
                    $fullPath,
                    $filename,
                    $mime,
                    null,
                    true
                );

                // 5. Jalankan kompresi otomatis (< 200 KB) dan simpan ke disk publik folder assets
                $compressedPath = $compressor->compressAndStore($uploadedFile, 'assets');

                $finalSizeKb = Storage::disk('public')->exists($compressedPath)
                    ? round(Storage::disk('public')->size($compressedPath) / 1024, 2)
                    : 0;

                // 6. Perbarui path foto aset di database
                $asset->update(['photo_path' => $compressedPath]);

                $this->info(sprintf(
                    '[✓] %s (%s): %s (%s KB) -> %s (%s KB)',
                    $asset->asset_code,
                    $asset->name,
                    $filename,
                    $initialSizeKb,
                    $compressedPath,
                    $finalSizeKb
                ));

                $processedCount++;
            } catch (\Throwable $e) {
                $this->error("[!] Gagal memproses [{$filename}] untuk aset [{$assetCode}]: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("====================================================");
        $this->info("Proses selesai: {$processedCount} foto berhasil dikompresi & terhubung, {$failedCount} dilewati/gagal.");
        $this->info("====================================================");

        return self::SUCCESS;
    }
}
