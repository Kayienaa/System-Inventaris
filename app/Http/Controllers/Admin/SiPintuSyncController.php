<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Services\SiPintuSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiPintuSyncController extends Controller
{
    /**
     * Memproses sinkronisasi data dari SiPintu Gateway ke database lokal secara langsung (sinkron).
     */
    public function sync(Request $request, SiPintuSyncService $syncService, AuditLogService $auditLogService): RedirectResponse
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $validated = $request->validate([
            'type' => 'nullable|string|in:all,students,teachers',
        ]);

        $type = $validated['type'] ?? 'all';

        if ($type === 'students') {
            $result = $syncService->syncStudents();
        } elseif ($type === 'teachers') {
            $result = $syncService->syncTeachers();
        } else {
            $result = $syncService->syncAll();
        }

        $admin = $request->user();
        if ($admin) {
            $auditLogService->record(
                actor: $admin,
                action: 'sipintu.synced',
                entity: $admin,
                oldValues: null,
                newValues: ['type' => $type, 'result' => $result],
                metadata: ['source' => 'web_admin']
            );
        }

        return redirect()->back()->with('success', 'Sinkronisasi berhasil! Data nomor telepon terbaru telah diperbarui dari SiPintu.');
    }
}

