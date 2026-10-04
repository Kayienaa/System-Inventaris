<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    /**
     * Tampilkan halaman panduan SOP operasional untuk Admin & Super Admin.
     */
    public function index(Request $request): View
    {
        // Proteksi peran: Wajib memiliki role admin atau super_admin
        if (! $request->user()?->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'Akses terbatas untuk Administrator dan Super Administrator.');
        }

        return view('admin.guides.index');
    }
}
