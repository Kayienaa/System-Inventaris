<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Generate valid Base64 data URL JPEG for image upload / compression testing.
     */
    protected function createTestBase64Image(int $width = 50, int $height = 50): string
    {
        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefilledrectangle($im, 0, 0, $width, $height, $white);
        ob_start();
        imagejpeg($im);
        $data = ob_get_clean();
        imagedestroy($im);

        return 'data:image/jpeg;base64,' . base64_encode($data);
    }
}
