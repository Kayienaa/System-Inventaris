<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AuditLogService;
use App\Services\SiPintuSyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncSiPintuJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [30, 120, 300];
    public int $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $type = 'all'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SiPintuSyncService $syncService, AuditLogService $audit): void
    {
        try {
            $summary = [];
            $isSuccess = false;

            if ($this->type === 'students') {
                $result = $syncService->syncStudents();
                $isSuccess = (bool) ($result['success'] ?? false);
                $summary = [
                    'type' => 'students',
                    'synced' => $result['synced'] ?? 0,
                    'created' => $result['created'] ?? 0,
                    'updated' => $result['updated'] ?? 0,
                    'errors' => $result['errors'] ?? 0,
                    'message' => $result['message'] ?? null,
                ];
            } elseif ($this->type === 'teachers') {
                $result = $syncService->syncTeachers();
                $isSuccess = (bool) ($result['success'] ?? false);
                $summary = [
                    'type' => 'teachers',
                    'synced' => $result['synced'] ?? 0,
                    'created' => $result['created'] ?? 0,
                    'updated' => $result['updated'] ?? 0,
                    'errors' => $result['errors'] ?? 0,
                    'message' => $result['message'] ?? null,
                ];
            } else {
                $result = $syncService->syncAll();
                $studentsSuccess = (bool) ($result['students']['success'] ?? false);
                $teachersSuccess = (bool) ($result['teachers']['success'] ?? false);
                $isSuccess = $studentsSuccess || $teachersSuccess;
                $summary = [
                    'type' => 'all',
                    'students' => $result['students'] ?? [],
                    'teachers' => $result['teachers'] ?? [],
                ];
            }

            if ($isSuccess) {
                $admin = User::role('admin')->first() ?? User::first();
                if ($admin) {
                    $audit->record(
                        $admin,
                        'sipintu.synced',
                        $admin,
                        null,
                        $summary,
                        ['type' => $this->type, 'source' => 'background_queue']
                    );
                }
                Log::info("SiPintu synchronization completed for type: {$this->type}", $summary);
            } else {
                Log::error("SiPintu synchronization returned failure for type: {$this->type}", $summary);
            }
        } catch (\Throwable $e) {
            Log::error('SiPintuSyncJob exception: ' . $e->getMessage(), [
                'type' => $this->type,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
