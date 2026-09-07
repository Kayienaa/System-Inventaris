<?php

namespace App\Http\Requests\Borrowings;

use App\Enums\AssetCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $borrowing = $this->route('borrowing');
        if ($this->user()?->hasAnyRole(['admin', 'super_admin'])) {
            return true;
        }

        return $borrowing && $this->user()?->id === $borrowing->borrower_user_id;
    }

    public function rules(): array
    {
        return ['checkout_condition' => ['required', Rule::enum(AssetCondition::class)]];
    }

    public function messages(): array
    {
        return ['checkout_condition.required' => 'Checkout condition is required.'];
    }
}
