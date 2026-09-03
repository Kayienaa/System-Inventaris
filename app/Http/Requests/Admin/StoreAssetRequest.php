<?php

namespace App\Http\Requests\Admin;

use App\Enums\AssetAvailabilityStatus;
use App\Enums\AssetCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'asset_code' => ['required', 'string', 'unique:assets,asset_code', 'max:50'],
            'serial_number' => ['required', 'string', 'unique:assets,serial_number', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', Rule::enum(AssetCondition::class)],
            'availability_status' => ['required', Rule::enum(AssetAvailabilityStatus::class)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'asset_category_id.required' => 'Kategori aset wajib dipilih.',
            'asset_category_id.exists' => 'Kategori aset yang dipilih tidak valid.',
            'asset_code.required' => 'Kode aset unik wajib diisi.',
            'asset_code.unique' => 'Kode aset ini sudah digunakan oleh aset lain.',
            'asset_code.max' => 'Kode aset maksimal 50 karakter.',
            'serial_number.required' => 'Nomor seri unit wajib diisi.',
            'serial_number.unique' => 'Nomor seri ini sudah terdaftar di sistem.',
            'serial_number.max' => 'Nomor seri maksimal 100 karakter.',
            'name.required' => 'Nama aset wajib diisi.',
            'name.max' => 'Nama aset maksimal 255 karakter.',
            'brand.required' => 'Merk/Brand wajib diisi.',
            'brand.max' => 'Merk maksimal 100 karakter.',
            'model.max' => 'Model maksimal 100 karakter.',
            'condition.required' => 'Kondisi fisik aset wajib dipilih.',
            'availability_status.required' => 'Status ketersediaan aset wajib dipilih.',
            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal adalah 2MB.',
        ];
    }
}
