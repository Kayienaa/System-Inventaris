<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\WhatsAppNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasAnyRole(['guru', 'siswa'])) {
            // Tolak perubahan jika user mencoba mengubah data identitas tanpa mengisi/mengirim nomor WhatsApp
            if (! $request->filled('phone')) {
                return Redirect::route('profile.edit')
                    ->with('error', 'Data akun Anda dikelola secara terpusat melalui SiPintu dan tidak dapat diubah langsung.');
            }

            // Normalisasi nomor telepon
            $normalized = WhatsAppNotificationService::normalizePhoneNumber($request->input('phone'));

            if ($user->hasRole('siswa')) {
                if (! $user->siswaProfile) {
                    return Redirect::route('profile.edit')
                        ->with('error', 'Profil Anda belum tersinkronisasi dari SiPintu Gateway. Silakan hubungi admin TEFA.');
                }

                $user->siswaProfile->update(['phone' => $normalized]);
            } elseif ($user->hasRole('guru')) {
                if (! $user->guruProfile) {
                    return Redirect::route('profile.edit')
                        ->with('error', 'Profil Anda belum tersinkronisasi dari SiPintu Gateway. Silakan hubungi admin TEFA.');
                }

                $user->guruProfile->update(['phone' => $normalized]);
            }

            return Redirect::route('profile.edit')
                ->with('status', 'profile-updated')
                ->with('success', 'Nomor telepon berhasil diperbarui.');
        }

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if ($request->user()->hasAnyRole(['guru', 'siswa'])) {
            return Redirect::route('profile.edit')
                ->with('error', 'Akun Anda dikelola secara terpusat melalui SiPintu.');
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
