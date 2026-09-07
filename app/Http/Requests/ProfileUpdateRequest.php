<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\IndonesianMobileNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        if ($user && $user->hasAnyRole(['guru', 'siswa'])) {
            // Jika user Siswa/Guru mengirim field phone, validasi format nomor seluler Indonesia
            if ($this->has('phone')) {
                return [
                    'phone' => [
                        'required',
                        'string',
                        new IndonesianMobileNumber(),
                    ],
                ];
            }

            // Jika tidak mengirim phone (misal coba ubah name/email), controller akan menolak dengan error SiPintu
            return [];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user?->id),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Masukkan nomor seluler Indonesia yang valid (contoh: 081234567890).',
        ];
    }
}
