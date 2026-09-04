<?php

namespace App\Http\Requests\Borrowings;

use App\Models\Borrowing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Borrowing::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('asset')) {
            $routeAsset = $this->route('asset');
            $assetId = $routeAsset instanceof \App\Models\Asset ? $routeAsset->id : $routeAsset;
            $this->merge([
                'asset_id' => $this->input('asset_id') ?: $assetId,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'integer', Rule::exists('assets', 'id')->whereNull('deleted_at')],
            'borrower_note' => ['nullable', 'string', 'max:1000'],
            'due_at' => ['nullable', 'date', 'after:now'],
            'borrowing_evidence' => ['nullable'],
            'borrowing_evidence_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_id.required' => 'Aset yang akan dipinjam wajib dipilih.',
            'asset_id.exists' => 'Aset yang dipilih tidak tersedia atau tidak ditemukan.',
            'due_at.after' => 'Waktu rencana pengembalian harus setelah waktu saat ini.',
            'borrowing_evidence.image' => 'Bukti peminjaman harus berupa file gambar.',
            'borrowing_evidence.max' => 'Ukuran file bukti peminjaman maksimal 5MB.',
        ];
    }
}
