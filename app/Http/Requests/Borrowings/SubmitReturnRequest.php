<?php

namespace App\Http\Requests\Borrowings;

use App\Models\Borrowing;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class SubmitReturnRequest extends FormRequest
{
    private const EVIDENCE_FOLDER = 'return-evidence/';

    public function authorize(): bool
    {
        $borrowing = $this->route('borrowing');

        return $borrowing instanceof Borrowing && ($this->user()?->can('submitReturn', $borrowing) ?? false);
    }

    public function rules(): array
    {
        return [
            'return_evidence_path' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (!is_string($value)) {
                        $fail('Jalur berkas bukti pengembalian tidak valid.');
                        return;
                    }

                    // Tolak jika ada path traversal (..) atau path absolut
                    if (str_contains($value, '..') || str_starts_with($value, '/') || str_starts_with($value, '\\') || preg_match('/^[a-zA-Z]:/', $value)) {
                        $fail('Jalur berkas bukti pengembalian tidak valid.');
                        return;
                    }

                    // Wajib diawali prefix return-evidence/
                    if (!str_starts_with($value, self::EVIDENCE_FOLDER)) {
                        $fail('Jalur berkas bukti pengembalian harus berada di direktori ' . self::EVIDENCE_FOLDER);
                        return;
                    }

                    // Pastikan file fisik benar-benar ada di disk public
                    if (!Storage::disk('public')->exists($value)) {
                        $fail('Berkas bukti pengembalian tidak ditemukan di penyimpanan publik.');
                        return;
                    }
                },
            ],
            'return_note' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_evidence_path.required' => 'Return evidence is required.',
        ];
    }
}
