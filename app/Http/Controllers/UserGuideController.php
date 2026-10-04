<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserGuideController extends Controller
{
    /**
     * Tampilkan halaman panduan peminjaman untuk siswa & guru.
     */
    public function index(Request $request): View
    {
        if (! $request->user()?->hasAnyRole(['siswa', 'guru'])) {
            abort(403, 'Akses halaman panduan hanya untuk peminjam (siswa & guru).');
        }

        return view('guides.user');
    }
}
