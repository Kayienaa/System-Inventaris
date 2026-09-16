<?php

namespace App\Http\Requests\Borrowings;

use App\Enums\AssetCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi detail ditangani di Controller / Policy
    }

    public function rules(): array
    {
        return [
            'checkout_condition' => ['nullable', Rule::enum(AssetCondition::class)],
            'borrowing_evidence' => ['nullable'],
            'borrowing_evidence_path' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'checkout_condition.enum' => 'Kondisi barang saat checkout tidak valid.',
        ];
    }
}
