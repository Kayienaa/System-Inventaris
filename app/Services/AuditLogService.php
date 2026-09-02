<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class AuditLogService
{
    /** @param array<string, mixed>|null $oldValues @param array<string, mixed>|null $newValues @param array<string, mixed>|null $metadata */
    public function record(?User $actor, string $action, Model $entity, ?array $oldValues = null, ?array $newValues = null, ?array $metadata = null): void
    {
        try {
            $meta = $metadata ?? [];
            if (request()) {
                $meta['ip_address'] = $meta['ip_address'] ?? request()->ip();
                $meta['user_agent'] = $meta['user_agent'] ?? request()->userAgent();
            }

            AuditLog::query()->create([
                'actor_user_id' => $actor?->id,
                'action' => $action,
                'entity_type' => $entity->getMorphClass(),
                'entity_id' => $entity->getKey(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'metadata' => $meta,
            ]);
        } catch (Throwable) {
            report(new \RuntimeException('Audit logging failed.'));
        }
    }
}
