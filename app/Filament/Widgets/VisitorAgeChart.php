<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasPeriodFilter;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use App\Models\VisitorCounter;

class VisitorAgeChart extends ChartWidget
{
    use HasWidgetShield, HasPeriodFilter, InteractsWithPageFilters;

    protected static ?int $sort = 3;

    public function getHeading(): ?string
    {
        return 'Distribusi Usia Pengunjung (' . $this->getPeriodLabel() . ')';
    }

    protected function getData(): array
    {
        $range = $this->getDateRange();

        $stats = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
            ->selectRaw('SUM(adult_count) as adults, SUM(teenager_count) as teenagers, SUM(child_count) as children')
            ->first();

        return [
            'datasets' => [
                [
                    'label' => 'Kategori Usia',
                    'data' => [
                        $stats->adults ?? 0,
                        $stats->teenagers ?? 0,
                        $stats->children ?? 0,
                    ],
                    'backgroundColor' => [
                        '#3b82f6', // blue-500
                        '#10b981', // emerald-500
                        '#f59e0b', // amber-500
                    ],
                ],
            ],
            'labels' => ['Dewasa', 'Remaja', 'Anak-anak'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
