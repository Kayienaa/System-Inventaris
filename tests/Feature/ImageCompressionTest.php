<?php

namespace Tests\Feature;

use App\Services\ImageCompressionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageCompressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploaded_image_is_resized_and_compressed_to_under_200kb(): void
    {
        Storage::fake('public');

        // Buat gambar simulasi besar berukuran 2000x1500 px
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
        $gdImage = imagecreatetruecolor(2000, 1500);
        $bgColor = imagecolorallocate($gdImage, 200, 220, 240);
        imagefilledrectangle($gdImage, 0, 0, 2000, 1500, $bgColor);
        imagejpeg($gdImage, $tmpFile, 100);
        imagedestroy($gdImage);

        $uploadedFile = new UploadedFile($tmpFile, 'large_camera.jpg', 'image/jpeg', null, true);

        $service = app(ImageCompressionService::class);
        $path = $service->compressAndStore($uploadedFile, 'test-evidence');

        $this->assertTrue(Storage::disk('public')->exists($path));

        $fileSize = Storage::disk('public')->size($path);
        // Ukuran harus <= 200 KB (204800 bytes)
        $this->assertLessThanOrEqual(200 * 1024, $fileSize);

        // Periksa dimensi maksimal gambar
        $storedContent = Storage::disk('public')->get($path);
        $imageInfo = getimagesizefromstring($storedContent);
        $this->assertLessThanOrEqual(1280, $imageInfo[0]);

        @unlink($tmpFile);
    }

    public function test_base64_webcam_image_is_compressed_under_200kb(): void
    {
        Storage::fake('public');

        // Buat canvas gambar 1600x1200 px
        $gdImage = imagecreatetruecolor(1600, 1200);
        $bgColor = imagecolorallocate($gdImage, 100, 150, 200);
        imagefilledrectangle($gdImage, 0, 0, 1600, 1200, $bgColor);
        ob_start();
        imagejpeg($gdImage, null, 95);
        $jpegData = ob_get_clean();
        imagedestroy($gdImage);

        $base64 = 'data:image/jpeg;base64,' . base64_encode($jpegData);

        $service = app(ImageCompressionService::class);
        $path = $service->compressAndStoreBase64($base64, 'test-evidence');

        $this->assertNotNull($path);
        $this->assertTrue(Storage::disk('public')->exists($path));

        $fileSize = Storage::disk('public')->size($path);
        // Ukuran harus <= 200 KB
        $this->assertLessThanOrEqual(200 * 1024, $fileSize);

        $storedContent = Storage::disk('public')->get($path);
        $imageInfo = getimagesizefromstring($storedContent);
        $this->assertLessThanOrEqual(1280, $imageInfo[0]);
    }
}
