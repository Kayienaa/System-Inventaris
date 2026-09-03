<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SiPintuSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SiPintuSyncController extends Controller
{
    public function __construct(
        protected SiPintuSyncService $syncService
    ) {}

    /**
     * Memproses sinkronisasi data dari SiPintu Gateway ke database lokal.
     */
    public function sync(Request $request): RedirectResponse
    {
        set_time_limit(0);
        ini_set('max_execution_time', '0');

        $validated = $request->validate([
            'type' => 'nullable|string|in:all,students,teachers',
        ]);

        $type = $validated['type'] ?? 'all';

        try {
            $processed = 0;
            $created = 0;
            $updated = 0;
            $errors = 0;

            if ($type === 'students') {
                $result = $this->syncService->syncStudents();
                if (! ($result['success'] ?? false)) {
                    return redirect()->back()->with('error', $result['message'] ?? 'Gagal menyinkronkan data siswa dari SiPintu.');
                }
                $processed = $result['synced'] ?? 0;
                $created = $result['created'] ?? 0;
                $updated = $result['updated'] ?? 0;
                $errors = $result['errors'] ?? 0;
            } elseif ($type === 'teachers') {
                $result = $this->syncService->syncTeachers();
                if (! ($result['success'] ?? false)) {
                    return redirect()->back()->with('error', $result['message'] ?? 'Gagal menyinkronkan data guru dari SiPintu.');
                }
                $processed = $result['synced'] ?? 0;
                $created = $result['created'] ?? 0;
                $updated = $result['updated'] ?? 0;
                $errors = $result['errors'] ?? 0;
            } else {
                $result = $this->syncService->syncAll();
                $studentsSuccess = $result['students']['success'] ?? false;
                $teachersSuccess = $result['teachers']['success'] ?? false;

                if (! $studentsSuccess && ! $teachersSuccess) {
                    $errMsg = ($result['students']['message'] ?? 'Gagal') . ' | ' . ($result['teachers']['message'] ?? 'Gagal');

                    return redirect()->back()->with('error', 'Gagal menyinkronkan data dari SiPintu: ' . $errMsg);
                }

                $processed = ($result['students']['synced'] ?? 0) + ($result['teachers']['synced'] ?? 0);
                $created = ($result['students']['created'] ?? 0) + ($result['teachers']['created'] ?? 0);
                $updated = ($result['students']['updated'] ?? 0) + ($result['teachers']['updated'] ?? 0);
                $errors = ($result['students']['errors'] ?? 0) + ($result['teachers']['errors'] ?? 0);
            }

            $summary = [
                'processed' => $processed,
                'created'   => $created,
                'updated'   => $updated,
                'errors'    => $errors,
            ];

            $message = "Sinkronisasi SiPintu berhasil! Total {$summary['processed']} data pengguna telah disinkronkan ke database lokal.";

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            Log::error('SiPintuSyncController exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan koneksi atau batas waktu (timeout) ke server SiPintu Gateway: ' . $e->getMessage());
        }
    }
}
