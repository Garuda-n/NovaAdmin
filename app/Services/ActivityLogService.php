<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    public static function log(
        string $module,
        string $action,
        ?int $referenceId = null,
        ?string $description = null,
        array $meta = []
    ): void {

        try {
            ActivityLog::create([
                'user_id'       => Auth::id(),
                'module'        => $module,
                'action'        => $action,
                'reference_id'  => $referenceId,
                'description'   => $description,
                'meta'          => empty($meta) ? null : json_encode($meta, JSON_UNESCAPED_UNICODE),
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Log silently
        }
    }
}