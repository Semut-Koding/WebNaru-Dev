<?php

namespace App\Filament\Widgets\Concerns;

use Carbon\Carbon;

trait HasPeriodFilter
{
    /**
     * Get the date range based on the period filter from the Dashboard.
     * Uses $this->pageFilters from InteractsWithPageFilters trait.
     *
     * @return array{start: Carbon, end: Carbon}
     */
    protected function getDateRange(): array
    {
        $filters = $this->pageFilters ?? [];
        $period = $filters['period'] ?? 'today';

        return match ($period) {
            'today' => [
                'start' => Carbon::today(),
                'end' => Carbon::today()->endOfDay(),
            ],
            'week' => [
                'start' => Carbon::now()->startOfWeek(),
                'end' => Carbon::now()->endOfWeek(),
            ],
            'month' => [
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now()->endOfMonth(),
            ],
            'year' => [
                'start' => Carbon::now()->startOfYear(),
                'end' => Carbon::now()->endOfYear(),
            ],
            'custom' => [
                'start' => Carbon::parse($filters['start_date'] ?? now()->startOfMonth()),
                'end' => Carbon::parse($filters['end_date'] ?? now())->endOfDay(),
            ],
            default => [
                'start' => Carbon::today(),
                'end' => Carbon::today()->endOfDay(),
            ],
        };
    }

    /**
     * Get a human-readable label for the current period.
     */
    protected function getPeriodLabel(): string
    {
        $filters = $this->pageFilters ?? [];
        $period = $filters['period'] ?? 'today';

        return match ($period) {
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
            'custom' => 'Kustom',
            default => 'Hari Ini',
        };
    }
}
