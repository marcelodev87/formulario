<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Carbon;

class ActivityLogger
{
    public function log(?User $actor, ?Institution $institution, string $entityType, ?int $entityId, string $action, array $before = [], array $after = []): void
    {
        $diff = $this->buildDiff($before, $after);

        ActivityLog::create([
            'actor_user_id' => $actor?->id,
            'institution_id' => $institution?->id,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'diff' => empty($diff['before']) && empty($diff['after']) ? null : $diff,
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * @return array{before: array<string, mixed>, after: array<string, mixed>}
     */
    private function buildDiff(array $before, array $after): array
    {
        $changes = [
            'before' => [],
            'after' => [],
        ];

        $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($keys as $key) {
            $oldValue = $before[$key] ?? null;
            $newValue = $after[$key] ?? null;

            if ($oldValue === $newValue) {
                continue;
            }

            $changes['before'][$key] = $oldValue;
            $changes['after'][$key] = $newValue;
        }

        return $changes;
    }
}