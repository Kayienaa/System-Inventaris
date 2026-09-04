<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncSiPintuJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiPintuSyncController extends Controller
{
    /**
     * Memproses sinkronisasi data dari SiPintu Gateway ke database lokal via antrean (Queue Job).
     */
    public function sync(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|string|in:all,students,teachers',
        ]);

        $type = $validated['type'] ?? 'all';

        SyncSiPintuJob::dispatch($type);

        return redirect()->back()->with('success', 'Sinkronisasi data akun SiPintu sedang diproses di latar belakang.');
    }
}

