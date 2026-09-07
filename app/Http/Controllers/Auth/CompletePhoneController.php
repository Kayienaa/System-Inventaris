<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\IndonesianMobileNumber;
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
                new IndonesianMobileNumber(),
            ],
        ], [
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $user = $request->user();
        $normalized = WhatsAppNotificationService::normalizePhoneNumber($request->input('phone'));

        if ($user->hasRole('siswa')) {
            if (! $user->siswaProfile) {
                return back()->with('error', 'Profil Anda belum tersinkronisasi dari SiPintu Gateway. Silakan hubungi admin TEFA.');
            }

            $user->siswaProfile->update(['phone' => $normalized]);
        } elseif ($user->hasRole('guru')) {
            if (! $user->guruProfile) {
                return back()->with('error', 'Profil Anda belum tersinkronisasi dari SiPintu Gateway. Silakan hubungi admin TEFA.');
            }

            $user->guruProfile->update(['phone' => $normalized]);
        }

        return redirect()->route('dashboard')->with('success', 'Nomor WhatsApp berhasil disimpan! Selamat datang di SITEFA.');
    }
}
