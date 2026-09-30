<?php

namespace App\Http\Requests\Borrowings;

use App\Models\Borrowing;
use Illuminate\Foundation\Http\FormRequest;

class SubmitReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        $borrowing = $this->route('borrowing');

        return $borrowing instanceof Borrowing && ($this->user()?->can('submitReturn', $borrowing) ?? false);
    }

    public function rules(): array
    {
        return [
            'borrowing_evidence'      => ['nullable', 'string', 'max:7000000'],
            'borrowing_evidence_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'return_evidence'         => ['nullable', 'string', 'max:7000000'],
            'return_evidence_file'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'return_evidence_path'    => ['prohibited'],
            'return_note'             => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'return_evidence_path.prohibited' => 'Jalur berkas langsung tidak diizinkan.',
        ];
    }
}
