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

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'login_log_id'  => session('login_log_id'), // 🔥 IMPORTANT LINE
            'module'        => $module,
            'action'        => $action,
            'reference_id'  => $referenceId,
            'description'   => $description,
            'meta'          => $meta,
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }
}