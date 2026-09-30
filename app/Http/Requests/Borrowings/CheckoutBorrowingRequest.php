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
            'borrowing_evidence' => ['nullable', 'string', 'max:7000000'],
            'borrowing_evidence_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'checkout_condition.enum' => 'Kondisi barang saat checkout tidak valid.',
        ];
    }
}
