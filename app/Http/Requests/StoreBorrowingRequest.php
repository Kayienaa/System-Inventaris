<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Sudah dibatasi middleware 'role:guru,siswa'
    }

    public function rules(): array
    {
        return [
            'item_id' => ['required', 'exists:items,id'],
            'tgl_kembali_rencana' => ['required', 'date', 'after:now'],
            'foto_siswa' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'foto_barang' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'include_charger' => ['sometimes', 'boolean'],
            'include_mouse' => ['sometimes', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }
}