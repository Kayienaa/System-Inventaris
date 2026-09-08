<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IndonesianMobileNumber implements ValidationRule
{
    /**
     * Jalankan validasi format nomor seluler Indonesia.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Format nomor WhatsApp tidak valid. Masukkan nomor seluler Indonesia yang valid (contoh: 081234567890).');

            return;
        }

        $cleaned = preg_replace('/[\s\-]/', '', trim($value));

        if (! preg_match('/^(\+62|62|0)8[1-9][0-9]{6,10}$/', $cleaned)) {
            $fail('Format nomor WhatsApp tidak valid. Masukkan nomor seluler Indonesia yang valid (contoh: 081234567890).');
        }
    }
}
