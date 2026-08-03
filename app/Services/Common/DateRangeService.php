<?php

namespace App\Services\Common;

use Carbon\Carbon;

class DateRangeService
{
    /**
     * Resolve date range from preset or custom dates.
     *
     * @param string|null $preset
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return array{from_date: Carbon, to_date: Carbon, preset: string}
     */
    public function resolve(?string $preset, ?string $fromDate, ?string $toDate): array
    {
        $preset = $preset ?: 'this_month';

        if ($preset === 'custom' && $fromDate && $toDate) {
            return [
                'from_date' => Carbon::parse($fromDate)->startOfDay(),
                'to_date'   => Carbon::parse($toDate)->endOfDay(),
                'preset'    => 'custom',
            ];
        }

        return match ($preset) {
            'today'        => [
                'from_date' => Carbon::today(),
                'to_date'   => Carbon::today()->endOfDay(),
                'preset'    => 'today',
            ],
            'yesterday'    => [
                'from_date' => Carbon::yesterday(),
                'to_date'   => Carbon::yesterday()->endOfDay(),
                'preset'    => 'yesterday',
            ],
            'last_7_days'  => [
                'from_date' => Carbon::today()->subDays(6),
                'to_date'   => Carbon::today()->endOfDay(),
                'preset'    => 'last_7_days',
            ],
            'last_30_days' => [
                'from_date' => Carbon::today()->subDays(29),
                'to_date'   => Carbon::today()->endOfDay(),
                'preset'    => 'last_30_days',
            ],
            'this_month'   => [
                'from_date' => Carbon::today()->startOfMonth(),
                'to_date'   => Carbon::today()->endOfDay(),
                'preset'    => 'this_month',
            ],
            'last_month'   => [
                'from_date' => Carbon::today()->subMonth()->startOfMonth(),
                'to_date'   => Carbon::today()->subMonth()->endOfMonth()->endOfDay(),
                'preset'    => 'last_month',
            ],
            'this_year'    => [
                'from_date' => Carbon::today()->startOfYear(),
                'to_date'   => Carbon::today()->endOfDay(),
                'preset'    => 'this_year',
            ],
            'last_year'    => [
                'from_date' => Carbon::today()->subYear()->startOfYear(),
                'to_date'   => Carbon::today()->subYear()->endOfYear()->endOfDay(),
                'preset'    => 'last_year',
            ],
            default        => [
                'from_date' => Carbon::today()->startOfMonth(),
                'to_date'   => Carbon::today()->endOfDay(),
                'preset'    => 'this_month',
            ],
        };
    }

    /**
     * Get available preset options for dropdown rendering.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public function getPresetOptions(): array
    {
        return [
            ['value' => 'today',        'label' => 'Today'],
            ['value' => 'yesterday',    'label' => 'Yesterday'],
            ['value' => 'last_7_days',  'label' => 'Last 7 Days'],
            ['value' => 'last_30_days', 'label' => 'Last 30 Days'],
            ['value' => 'this_month',   'label' => 'This Month'],
            ['value' => 'last_month',   'label' => 'Last Month'],
            ['value' => 'this_year',    'label' => 'This Year'],
            ['value' => 'last_year',    'label' => 'Last Year'],
            ['value' => 'custom',       'label' => 'Custom Range'],
        ];
    }
}
