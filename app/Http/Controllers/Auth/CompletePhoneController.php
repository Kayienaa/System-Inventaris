<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompletePhoneController extends Controller
{
    /**
     * Tampilkan form pengisian nomor WhatsApp.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Jika user bukan Siswa atau Guru, atau sudah melengkapi nomor, alihkan ke dashboard
        if (! $user->hasAnyRole(['siswa', 'guru'])) {
            return redirect()->route('dashboard');
        }

        $currentPhone = $user->siswaProfile?->phone ?: $user->guruProfile?->phone;
        if (! empty($currentPhone)) {
            return redirect()->route('dashboard');
        }

        return view('auth.complete-phone', [
            'user' => $user,
        ]);
    }

    /**
     * Validasi, normalisasi, dan simpan nomor WhatsApp pengguna.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => [
                'required',
                'string',
                'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/',
            ],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Masukkan nomor seluler Indonesia yang valid (contoh: 081234567890).',
        ]);

        $user = $request->user();
        $normalized = WhatsAppNotificationService::normalizePhoneNumber($request->input('phone'));

        if ($user->hasRole('siswa')) {
            if ($user->siswaProfile) {
                $user->siswaProfile->update(['phone' => $normalized]);
            } else {
                $user->siswaProfile()->create([
                    'nis' => 'S-' . $user->id,
                    'phone' => $normalized,
                ]);
            }
        } elseif ($user->hasRole('guru')) {
            if ($user->guruProfile) {
                $user->guruProfile->update(['phone' => $normalized]);
            } else {
                $user->guruProfile()->create([
                    'nip' => 'G-' . $user->id,
                    'phone' => $normalized,
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Nomor WhatsApp berhasil disimpan! Selamat datang di TE-VAULT.');
    }
}
