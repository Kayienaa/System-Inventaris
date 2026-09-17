<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuditLogService;
use App\Services\SiPintuSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiPintuSyncController extends Controller
{
    /**
     * Memproses sinkronisasi data dari SiPintu Gateway secara langsung (synchronous).
     */
    public function sync(Request $request, SiPintuSyncService $syncService, ?AuditLogService $auditLogService = null): RedirectResponse
    {
        // Hilangkan batas waktu eksekusi agar pemrosesan data ribuan akun berjalan tuntas
        set_time_limit(0);
        ini_set('max_execution_time', '0');

        $auditLogService ??= app(AuditLogService::class);
        $type = $request->input('type', 'all');

        try {
            if ($type === 'students') {
                $result = $syncService->syncStudents();
                $message = "Sinkronisasi berhasil! Data siswa telah diperbarui.";
            } elseif ($type === 'teachers') {
                $result = $syncService->syncTeachers();
                $message = "Sinkronisasi berhasil! Data dewan guru telah diperbarui.";
            } else {
                $result = $syncService->syncAll();
                $message = "Sinkronisasi berhasil! Seluruh data siswa dan dewan guru telah diperbarui.";
            }

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

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('Gagal sinkronisasi SiPintu: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal terhubung atau memproses sinkronisasi SiPintu: ' . $e->getMessage());
        }
    }
}
