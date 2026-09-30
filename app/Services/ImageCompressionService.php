<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Laravel\Facades\Image;

class ImageCompressionService
{
    private const ALLOWED_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP];
    private const MAX_PIXELS = 12_000_000;      // 12 MP
    private const MAX_BYTES  = 5 * 1024 * 1024; // 5 MB

    public const MAX_WIDTH = 1280;        // Maksimal dimensi foto (px)
    public const TARGET_MAX_KB = 200;      // Batas atas ukuran file (200 KB)
    public const TARGET_MIN_KB = 100;      // Batas bawah rekomendasi (100 KB)
    public const MAX_QUALITY = 75;         // Batas atas rentang kualitas kompresi (65 - 75)
    public const MIN_QUALITY = 65;         // Batas bawah rentang kualitas kompresi standar (65 - 75)
    public const DEFAULT_QUALITY = 75;     // Level kompresi standar awal

    private function fail(string $msg): never
    {
        throw ValidationException::withMessages(['evidence' => $msg]);
    }

    private function assertSafe(array|false $info, int $bytes): void
    {
        if ($bytes > self::MAX_BYTES) {
            $this->fail('Ukuran foto maksimal 5 MB.');
        }
        if ($info === false || ! in_array($info[2], self::ALLOWED_TYPES, true)) {
            $this->fail('File harus berupa foto JPG, PNG, atau WEBP.');
        }
        if ($info[0] * $info[1] > self::MAX_PIXELS) {
            $this->fail('Resolusi foto terlalu besar.');
        }
    }

    /**
     * Resize + kompres foto UploadedFile agar ukurannya konsisten di rentang 100 KB – 200 KB.
     * Mengembalikan path relatif untuk disimpan di kolom database.
     */
    public function compressAndStore(UploadedFile $file, string $folder = 'uploads'): string
    {
        $this->assertSafe(@getimagesize($file->getRealPath()), $file->getSize());

        try {
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $image = Image::decode($file->getRealPath());
                $image->scaleDown(width: self::MAX_WIDTH, height: self::MAX_WIDTH);

                $clientExt = strtolower($file->getClientOriginalExtension());
                $format = ($clientExt === 'webp') ? 'webp' : 'jpg';

                // Iterasi kompresi bertahap antara 75 turun ke 65
                $quality = self::MAX_QUALITY; // 75
                $encoded = $image->encodeUsingFileExtension($format, quality: $quality);

                while (strlen((string) $encoded) > (self::TARGET_MAX_KB * 1024) && $quality > self::MIN_QUALITY) {
                    $quality -= 2;
                    $encoded = $image->encodeUsingFileExtension($format, quality: $quality);
                }

                // Jika pada kualitas 65 masih di atas batas maksimal 200 KB, lanjutkan kompresi bertahap (minimal quality 40)
                while (strlen((string) $encoded) > (self::TARGET_MAX_KB * 1024) && $quality > 40) {
                    $quality -= 5;
                    $encoded = $image->encodeUsingFileExtension($format, quality: $quality);
                }

                $ext = ($format === 'webp') ? 'webp' : 'jpg';
                $filename = $folder . '/' . now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
                Storage::disk('public')->put($filename, (string) $encoded);

                return $filename;
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Gagal kompresi file upload pada ImageCompressionService: ' . $e->getMessage());
            $this->fail('Gagal memproses file foto.');
        }

        $this->fail('Driver pemrosesan gambar tidak tersedia.');
    }

    /**
     * Resize + kompres data foto Base64 Data URL atau binary string agar konsisten di rentang 100 KB – 200 KB.
     */
    public function compressAndStoreBase64(string $dataUrl, string $folder = 'evidence'): ?string
    {
        if (! preg_match('#^data:image/(jpeg|png|webp);base64,#', $dataUrl, $m)) {
            $this->fail('Format foto kamera tidak valid.');
        }
        $encoded = substr($dataUrl, strlen($m[0]));
        if (strlen($encoded) > 7_000_000) {
            $this->fail('Foto terlalu besar.');
        }
        $binary = base64_decode($encoded, true);
        if ($binary === false) {
            $this->fail('Data foto rusak.');
        }
        $this->assertSafe(@getimagesizefromstring($binary), strlen($binary));

        $format = ($m[1] === 'webp') ? 'webp' : 'jpg';

        try {
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $image = Image::decode($binary);
                $image->scaleDown(width: self::MAX_WIDTH, height: self::MAX_WIDTH);

                // Iterasi kompresi bertahap antara 75 turun ke 65
                $quality = self::MAX_QUALITY; // 75
                $encodedImg = $image->encodeUsingFileExtension($format, quality: $quality);

                while (strlen((string) $encodedImg) > (self::TARGET_MAX_KB * 1024) && $quality > self::MIN_QUALITY) {
                    $quality -= 2;
                    $encodedImg = $image->encodeUsingFileExtension($format, quality: $quality);
                }

                while (strlen((string) $encodedImg) > (self::TARGET_MAX_KB * 1024) && $quality > 40) {
                    $quality -= 5;
                    $encodedImg = $image->encodeUsingFileExtension($format, quality: $quality);
                }

                $ext = ($format === 'webp') ? 'webp' : 'jpg';
                $finalFilename = $folder . '/' . now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
                Storage::disk('public')->put($finalFilename, (string) $encodedImg);

                return $finalFilename;
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Gagal decode gambar base64 pada ImageCompressionService: ' . $e->getMessage());
            $this->fail('Gagal memproses foto kamera.');
        }

        $this->fail('Driver pemrosesan gambar tidak tersedia.');
    }

    /**
     * Simpan dan kompres foto bukti peminjaman/pengembalian baik dari file upload maupun webcam base64.
     */
    public function compressAndStoreEvidence(Request $request, string $inputName, string $folder): ?string
    {
        // 1. File Upload konvensional
        if ($request->hasFile($inputName)) {
            return $this->compressAndStore($request->file($inputName), $folder);
        }
        if ($request->hasFile($inputName . '_file')) {
            return $this->compressAndStore($request->file($inputName . '_file'), $folder);
        }

        // 2. Webcam Snapshot (Base64)
        $base64 = $request->input($inputName);
        if (is_string($base64) && str_starts_with($base64, 'data:image/')) {
            return $this->compressAndStoreBase64($base64, $folder);
        }

        return null;
    }
}