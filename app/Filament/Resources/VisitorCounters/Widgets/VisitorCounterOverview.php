<?php

namespace App\Filament\Resources\VisitorCounters\Widgets;

use App\Models\VisitorCounter;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class VisitorCounterOverview extends StatsOverviewWidget
{

    protected function getStats(): array
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth()->toDateString();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        // 1 query — hanya agregat yang dibutuhkan
        $stats = VisitorCounter::query()
            ->selectRaw("
            SUM(CASE WHEN date = ? THEN adult_count + teenager_count + child_count ELSE 0 END) as total_today,
            SUM(CASE WHEN date = ? THEN adult_count + teenager_count + child_count ELSE 0 END) as total_yesterday,
            SUM(CASE WHEN date >= ? THEN adult_count + teenager_count + child_count ELSE 0 END) as total_week,
            SUM(CASE WHEN date >= ? THEN adult_count + teenager_count + child_count ELSE 0 END) as total_month,
            SUM(CASE WHEN date BETWEEN ? AND ? THEN adult_count + teenager_count + child_count ELSE 0 END) as total_last_month,
            COUNT(CASE WHEN date = ? AND is_group = 1 THEN 1 END) as groups_today,
            COUNT(CASE WHEN date = ? THEN 1 END) as entries_today
        ", [
                $today,
                $yesterday,
                $weekStart,
                $monthStart,
                $lastMonthStart,
                $lastMonthEnd,
                $today,
                $today,
            ])
            ->first();

        $totalToday = (int) ($stats->total_today ?? 0);
        $totalYesterday = (int) ($stats->total_yesterday ?? 0);
        $totalWeek = (int) ($stats->total_week ?? 0);
        $totalMonth = (int) ($stats->total_month ?? 0);
        $totalLastMonth = (int) ($stats->total_last_month ?? 0);
        $groupsToday = (int) ($stats->groups_today ?? 0);
        $entriesToday = (int) ($stats->entries_today ?? 0);

        [$todayDesc, $todayIcon, $todayColor] = $this->getChangeInfo($totalToday, $totalYesterday);
        [$monthDesc, $monthIcon, $monthColor] = $this->getChangeInfo($totalMonth, $totalLastMonth);

        $dailyChart = $this->getDailyTotalChart(7);
        $monthlyChart = $this->getMonthlyTotalChart(6);
        $groupChart = $this->getDailyGroupChart(7);

        $daysElapsed = Carbon::today()->diffInDays(Carbon::now()->startOfMonth()) + 1;
        $avgPerDay = $daysElapsed > 0 ? (int) round($totalMonth / $daysElapsed) : 0;

        return [
            // CARD 1: Total hari ini — tanpa komposisi
            Stat::make('Total Pengunjung Hari Ini', number_format($totalToday, 0, ',', '.'))
                ->description($todayDesc . ' · Minggu ini: ' . number_format($totalWeek, 0, ',', '.'))
                ->descriptionIcon($todayIcon)
                ->color($todayColor)
                ->chart($dailyChart),

            // CARD 2: Bulan ini vs bulan lalu
            Stat::make('Total Pengunjung Bulan Ini', number_format($totalMonth, 0, ',', '.'))
                ->description($monthDesc . ' vs bulan lalu (' . number_format($totalLastMonth, 0, ',', '.') . ')')
                ->descriptionIcon($monthIcon)
                ->color($monthColor)
                ->chart($monthlyChart),

            // CARD 3: Rombongan hari ini
            Stat::make('Rombongan Hari Ini', $groupsToday . ' Grup')
                ->description(
                    'Dari ' . $entriesToday . ' entri · ' .
                    ($entriesToday > 0
                        ? round(($groupsToday / $entriesToday) * 100, 1) . '% adalah rombongan'
                        : 'Belum ada entri')
                )
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart($groupChart),

            // CARD 4: Rata-rata per hari bulan ini
            Stat::make(
                'Rata-rata Per Hari (' . Carbon::now()->translatedFormat('F') . ')',
                number_format($avgPerDay, 0, ',', '.') . ' orang/hari'
            )
                ->description('Berdasarkan ' . $daysElapsed . ' hari berjalan bulan ini')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info')
                ->chart($dailyChart),
        ];
    }

    private function getChangeInfo(int $current, int $previous): array
    {
        if ($previous === 0 && $current === 0)
            return ['Belum ada data', 'heroicon-m-minus', 'gray'];
        if ($previous === 0 && $current > 0)
            return ['Pengunjung baru', 'heroicon-m-arrow-trending-up', 'success'];

        $change = round((($current - $previous) / $previous) * 100, 1);
        if ($change > 0)
            return [$change . '% kenaikan', 'heroicon-m-arrow-trending-up', 'success'];
        if ($change < 0)
            return [abs($change) . '% penurunan', 'heroicon-m-arrow-trending-down', 'danger'];
        return ['Sama dengan sebelumnya', 'heroicon-m-minus', 'info'];
    }

    private function getDailyTotalChart(int $days): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $data = VisitorCounter::query()
            ->selectRaw('date, SUM(adult_count + teenager_count + child_count) as total')
            ->where('date', '>=', $start)
            ->groupBy('date')
            ->pluck('total', 'date');

        return collect(range($days - 1, 0))
            ->map(fn($d) => (int) ($data[Carbon::today()->subDays($d)->toDateString()] ?? 0))
            ->toArray();
    }

    private function getDailyGroupChart(int $days): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $data = VisitorCounter::query()
            ->selectRaw('date, COUNT(CASE WHEN is_group = 1 THEN 1 END) as total')
            ->where('date', '>=', $start)
            ->groupBy('date')
            ->pluck('total', 'date');

        return collect(range($days - 1, 0))
            ->map(fn($d) => (int) ($data[Carbon::today()->subDays($d)->toDateString()] ?? 0))
            ->toArray();
    }

    private function getMonthlyTotalChart(int $months): array
    {
        $start = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $data = VisitorCounter::query()
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, SUM(adult_count + teenager_count + child_count) as total")
            ->where('date', '>=', $start)
            ->groupBy('month')
            ->pluck('total', 'month');

        return collect(range($months - 1, 0))
            ->map(fn($m) => (int) ($data[Carbon::now()->subMonths($m)->format('Y-m')] ?? 0))
            ->toArray();
    }
}