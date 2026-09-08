<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageCompressionService
{
    public const MAX_WIDTH = 1280;        // Maksimal dimensi foto (px)
    public const TARGET_MAX_KB = 200;      // Batas atas ukuran file (200 KB)
    public const TARGET_MIN_KB = 100;      // Batas bawah rekomendasi (100 KB)
    public const MAX_QUALITY = 75;         // Batas atas rentang kualitas kompresi (65 - 75)
    public const MIN_QUALITY = 65;         // Batas bawah rentang kualitas kompresi standar (65 - 75)
    public const DEFAULT_QUALITY = 75;     // Level kompresi standar awal

    /**
     * Resize + kompres foto UploadedFile agar ukurannya konsisten di rentang 100 KB – 200 KB.
     * Mengembalikan path relatif untuk disimpan di kolom database.
     */
    public function compressAndStore(UploadedFile $file, string $folder = 'uploads'): string
    {
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
        } catch (\Throwable $e) {
            // Fallback ke penyimpanan default jika proses kompresi terkendala driver / format
            Log::warning('Gagal kompresi file upload pada ImageCompressionService: ' . $e->getMessage());
        }

        return $file->store($folder, 'public');
    }

    /**
     * Resize + kompres data foto Base64 Data URL atau binary string agar konsisten di rentang 100 KB – 200 KB.
     */
    public function compressAndStoreBase64(string $base64OrBinary, string $folder = 'evidence'): ?string
    {
        $data = $base64OrBinary;
        $format = 'jpg';

        if (str_starts_with($base64OrBinary, 'data:image/')) {
            @[$header, $payload] = explode(';', $base64OrBinary, 2);
            @[, $payload] = explode(',', $payload, 2);
            if ($payload) {
                $decoded = base64_decode($payload);
                if ($decoded === false) {
                    return null;
                }
                $data = $decoded;
            }
            if (str_contains($header, 'webp')) {
                $format = 'webp';
            }
        }

        try {
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $image = Image::decode($data);
                $image->scaleDown(width: self::MAX_WIDTH, height: self::MAX_WIDTH);

                // Iterasi kompresi bertahap antara 75 turun ke 65
                $quality = self::MAX_QUALITY; // 75
                $encoded = $image->encodeUsingFileExtension($format, quality: $quality);

                while (strlen((string) $encoded) > (self::TARGET_MAX_KB * 1024) && $quality > self::MIN_QUALITY) {
                    $quality -= 2;
                    $encoded = $image->encodeUsingFileExtension($format, quality: $quality);
                }

                while (strlen((string) $encoded) > (self::TARGET_MAX_KB * 1024) && $quality > 40) {
                    $quality -= 5;
                    $encoded = $image->encodeUsingFileExtension($format, quality: $quality);
                }

                $ext = ($format === 'webp') ? 'webp' : 'jpg';
                $finalFilename = $folder . '/' . now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
                Storage::disk('public')->put($finalFilename, (string) $encoded);

                return $finalFilename;
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal decode gambar base64 pada ImageCompressionService: ' . $e->getMessage());
            return null;
        }

        return null;
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