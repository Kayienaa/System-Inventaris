<?php

namespace App\Http\Requests\Borrowings;

use Illuminate\Foundation\Http\FormRequest;

class ApproveBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['admin', 'super_admin']) ?? false;
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}
