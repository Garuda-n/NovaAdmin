<?php

namespace App\Services\Common;

use Illuminate\Http\Request;

class ReportFilterService
{
    /**
     * Extract and sanitize filter values from a request.
     *
     * Only keys listed in $allowedFilters are returned.
     * String values are trimmed; null/empty values are excluded.
     *
     * @param Request $request
     * @param array<int, string> $allowedFilters
     * @return array<string, mixed>
     */
    public function sanitize(Request $request, array $allowedFilters): array
    {
        $filters = [];

        foreach ($allowedFilters as $key) {
            if ($request->filled($key)) {
                $value = $request->input($key);
                $filters[$key] = is_string($value) ? trim($value) : $value;
            }
        }

        return $filters;
    }
}
