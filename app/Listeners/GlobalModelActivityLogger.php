<?php

namespace App\Listeners;

use Illuminate\Database\Eloquent\Model;
use App\Services\ActivityLogService;
use App\Models\ActivityLog;

class GlobalModelActivityLogger
{
    public function handle($event): void
    {
        // 🚫 Block artisan / system operations
        if (app()->runningInConsole()) {
            return;
        }

        // 🚫 Ensure it's a model event
        if (!isset($event->model) || !($event->model instanceof Model)) {
            return;
        }

        $model = $event->model;

        // 🚫 Don't log ActivityLog itself (prevents infinite loop)
        if ($model instanceof ActivityLog) {
            return;
        }

        $class = class_basename($model);

        // 🎯 Detect action type
        $action = match (true) {
            str_contains(get_class($event), 'Created') => 'CREATED',
            str_contains(get_class($event), 'Updated') => 'UPDATED',
            str_contains(get_class($event), 'Deleted') => 'DELETED',
            default => 'UNKNOWN',
        };

        // 🧹 Remove noise fields
        $changes = collect($model->getChanges())
            ->reject(fn ($value, $key) => in_array($key, [
                'updated_at',
                'created_at',
                'remember_token',
                'email_verified_at',
            ]))
            ->toArray();

        // 🚫 Skip empty changes (no meaningful update)
        if (empty($changes)) {
            return;
        }

        // 🧾 Final log
        ActivityLogService::log(
            module: $class,
            action: $action,
            referenceId: $model->id ?? null,
            description: "{$class} {$action}",
            meta: [
                'changes' => $changes,
            ]
        );
    }
}