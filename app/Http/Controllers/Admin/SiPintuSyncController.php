<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SyncSiPintuJob;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiPintuSyncController extends Controller
{
    /**
     * Memproses sinkronisasi data dari SiPintu Gateway via background queue.
     */
    public function sync(Request $request, AuditLogService $auditLogService): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|string|in:all,students,teachers',
        ]);

        $type = $validated['type'] ?? 'all';

        SyncSiPintuJob::dispatch($type);

        $admin = $request->user();
        if ($admin) {
            $auditLogService->record(
                actor: $admin,
                action: 'sipintu.sync_requested',
                entity: $admin,
                oldValues: null,
                newValues: ['type' => $type],
                metadata: ['source' => 'web_admin']
            );
        }

        return redirect()->back()->with('success', 'Sinkronisasi telah dijadwalkan dan sedang berjalan di background. Data akan diperbarui dalam beberapa saat — pastikan queue worker aktif (php artisan queue:work).');
    }
}
