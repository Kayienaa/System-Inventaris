<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminSwitchController extends Controller
{
    /**
     * Berpindah hak akses secara instan ke Super Admin khusus untuk akun Pak Agung.
     */
    public function switchToSuperAdmin(Request $request): RedirectResponse
    {
        $currentUser = $request->user();

        // Verifikasi akun Pak Agung berdasarkan email, NIP, atau nama
        $isPakAgung = $currentUser && (
            $currentUser->email === 'agungmikro2@gmail.com'
            || $currentUser->guruProfile?->nip === '198103302010011016'
            || str_contains(strtolower($currentUser->name), 'agung')
        );

        if (! $isPakAgung) {
            abort(403, 'Akses pintasan Super Admin ini khusus diperuntukkan bagi akun Pak Agung.');
        }

        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Kata sandi Super Admin wajib diisi.',
        ]);

        $superAdmin = User::where('email', 'AdminInventaris@gmail.com')->first();

        if (! $superAdmin || ! Hash::check($request->input('password'), $superAdmin->password)) {
            return back()
                ->withInput()
                ->with('error', 'Kata sandi Super Admin salah')
                ->with('super_admin_switch_failed', true);
        }

        // Login ke akun Super Admin resmi
        Auth::login($superAdmin);
        $request->session()->regenerate();

        return redirect()->route('admin.borrowings.index')
            ->with('success', 'Berhasil beralih ke hak akses Super Administrator SITEFA.');
    }
}
