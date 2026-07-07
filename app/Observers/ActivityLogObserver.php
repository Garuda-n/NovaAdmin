<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityLogService;

class ActivityLogObserver
{
    public function created(User $user): void
    {
        ActivityLogService::log(
            module: 'User',
            action: 'CREATED',
            referenceId: $user->id,
            description: 'User created',
            meta: $user->toArray()
        );
    }

    public function updated(User $user): void
    {
        ActivityLogService::log(
            module: 'User',
            action: 'UPDATED',
            referenceId: $user->id,
            description: 'User updated',
            meta: [
                'changes' => $user->getChanges()
            ]
        );
    }

    public function deleted(User $user): void
    {
        ActivityLogService::log(
            module: 'User',
            action: 'DELETED',
            referenceId: $user->id,
            description: 'User deleted'
        );
    }
}