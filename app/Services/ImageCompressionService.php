<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageCompressionService
{
    private const MAX_WIDTH = 1280;      // px
    private const MAX_SIZE_KB = 1024;    // 1 MB
    private const START_QUALITY = 80;

    /**
     * Resize + kompres foto agar ukurannya <= 1MB, lalu simpan ke storage.
     * Mengembalikan path relatif untuk disimpan di kolom database.
     */
    public function compressAndStore(UploadedFile $file, string $folder): string
    {
        $image = Image::read($file)->scaleDown(width: self::MAX_WIDTH);

        $quality = self::START_QUALITY;
        do {
            $encoded = $image->toJpeg($quality);
            $quality -= 10;
        } while (strlen((string) $encoded) > self::MAX_SIZE_KB * 1024 && $quality > 30);

        $filename = $folder.'/'.now()->format('Ymd_His').'_'.Str::random(8).'.jpg';
        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}