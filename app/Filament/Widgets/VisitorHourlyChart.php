<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasPeriodFilter;
use App\Models\Setting;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use App\Models\VisitorCounter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VisitorHourlyChart extends ChartWidget
{
    use HasWidgetShield, HasPeriodFilter, InteractsWithPageFilters;

    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 'full';

    public function getHeading(): ?string
    {
        $filters = $this->pageFilters ?? [];
        $period = $filters['period'] ?? 'today';

        return match ($period) {
            'today' => 'Grafik Pengunjung (Hari Ini) per Jam',
            'week' => 'Grafik Pengunjung (Minggu Ini) per Hari',
            'month' => 'Grafik Pengunjung (Bulan Ini) per Tanggal',
            'year' => 'Grafik Pengunjung (Tahun Ini) per Bulan',
            'custom' => 'Grafik Pengunjung (Kustom: ' . ($filters['start_date'] ?? '?') . ' s/d ' . ($filters['end_date'] ?? '?') . ')',
            default => 'Grafik Pengunjung (Hari Ini) per Jam',
        };
    }

    protected function getData(): array
    {
        $range = $this->getDateRange();
        $filters = $this->pageFilters ?? [];
        $period = $filters['period'] ?? 'today';

        return match ($period) {
            'today' => $this->getTodayData($range),
            'week' => $this->getWeekData($range),
            'month' => $this->getMonthData($range),
            'year' => $this->getYearData($range),
            'custom' => $this->getCustomData($range),
            default => $this->getTodayData($range),
        };
    }

    /**
     * Today: group by HOUR, labels = operational hours (08:00 - 17:00 / 07:00 - 18:00)
     */
    private function getTodayData(array $range): array
    {
        $isWeekend = Carbon::today()->isWeekend();

        $openKey = $isWeekend ? 'operational_hour_weekend_open' : 'operational_hour_weekday_open';
        $closeKey = $isWeekend ? 'operational_hour_weekend_close' : 'operational_hour_weekday_close';

        $openHour = (int) explode(':', Setting::where('key', $openKey)->value('value') ?? '08:00')[0];
        $closeHour = (int) explode(':', Setting::where('key', $closeKey)->value('value') ?? '17:00')[0];

        $stats = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(adult_count + teenager_count + child_count) as total')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        $labels = [];
        $data = [];

        for ($h = $openHour; $h <= $closeHour; $h++) {
            $labels[] = sprintf('%02d.00', $h);
            $data[] = $stats[$h] ?? 0;
        }

        return $this->buildDataset($labels, $data);
    }

    /**
     * Week: group by DAYOFWEEK, labels = Sen, Sel, Rab, Kam, Jum, Sab, Min
     */
    private function getWeekData(array $range): array
    {
        $stats = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
            ->select(
                DB::raw('DAYOFWEEK(date) as dow'),
                DB::raw('SUM(adult_count + teenager_count + child_count) as total')
            )
            ->groupBy('dow')
            ->orderBy('dow')
            ->pluck('total', 'dow')
            ->toArray();

        // MySQL DAYOFWEEK: 1=Sunday, 2=Monday, ..., 7=Saturday
        // Reorder to Monday-first
        $dayNames = [2 => 'Sen', 3 => 'Sel', 4 => 'Rab', 5 => 'Kam', 6 => 'Jum', 7 => 'Sab', 1 => 'Min'];

        $labels = [];
        $data = [];

        foreach ($dayNames as $dow => $name) {
            $labels[] = $name;
            $data[] = $stats[$dow] ?? 0;
        }

        return $this->buildDataset($labels, $data);
    }

    /**
     * Month: group by DAY, labels = 1, 2, 3, ... 28/30/31
     */
    private function getMonthData(array $range): array
    {
        $stats = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
            ->select(
                DB::raw('DAY(date) as day'),
                DB::raw('SUM(adult_count + teenager_count + child_count) as total')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $daysInMonth = Carbon::now()->daysInMonth;
        $labels = [];
        $data = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $labels[] = (string) $d;
            $data[] = $stats[$d] ?? 0;
        }

        return $this->buildDataset($labels, $data);
    }

    /**
     * Year: group by MONTH, labels = Jan, Feb, ... Des
     */
    private function getYearData(array $range): array
    {
        $stats = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(adult_count + teenager_count + child_count) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $labels = [];
        $data = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = $monthNames[$m - 1];
            $data[] = $stats[$m] ?? 0;
        }

        return $this->buildDataset($labels, $data);
    }

    /**
     * Custom: auto-select grouping based on date range duration
     */
    private function getCustomData(array $range): array
    {
        $start = Carbon::parse($range['start']);
        $end = Carbon::parse($range['end']);
        $diffDays = $start->diffInDays($end);

        // 1 day or same day → group by HOUR
        if ($diffDays <= 1) {
            return $this->getTodayData($range);
        }

        // ≤ 31 days → group by DATE with formatted labels
        if ($diffDays <= 31) {
            $stats = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
                ->select(
                    DB::raw('DATE(date) as day_date'),
                    DB::raw('SUM(adult_count + teenager_count + child_count) as total')
                )
                ->groupBy('day_date')
                ->orderBy('day_date')
                ->pluck('total', 'day_date')
                ->toArray();

            $labels = [];
            $data = [];
            $cursor = $start->copy();

            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m-d');
                $labels[] = $cursor->format('d/m');
                $data[] = $stats[$key] ?? 0;
                $cursor->addDay();
            }

            return $this->buildDataset($labels, $data);
        }

        // > 31 days → group by MONTH
        $stats = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
            ->select(
                DB::raw('DATE_FORMAT(date, "%Y-%m") as month_key'),
                DB::raw('SUM(adult_count + teenager_count + child_count) as total')
            )
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total', 'month_key')
            ->toArray();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $labels = [];
        $data = [];
        $cursor = $start->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m');
            $labels[] = $monthNames[$cursor->month - 1] . ' ' . $cursor->format('Y');
            $data[] = $stats[$key] ?? 0;
            $cursor->addMonth();
        }

        return $this->buildDataset($labels, $data);
    }

    private function buildDataset(array $labels, array $data): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total Pengunjung',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'min' => 0,
                ],
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'autoSkip' => true,
                        'maxTicksLimit' => 15,
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
