<?php

namespace App\Filament\Resources\AttractionCounters\Widgets;

use App\Models\AttractionCounter;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AttractionCounterOverview extends StatsOverviewWidget
{

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Stat queries — sudah efisien
        $totalToday = AttractionCounter::whereDate('date', $today)->sum('count');
        $totalYesterday = AttractionCounter::whereDate('date', $yesterday)->sum('count');
        $totalWeek = AttractionCounter::whereBetween('date', [$thisWeekStart, $today])->sum('count');
        $totalMonth = AttractionCounter::whereBetween('date', [$thisMonthStart, $today])->sum('count');
        $totalLastMonth = AttractionCounter::whereBetween('date', [$lastMonthStart, $lastMonthEnd])->sum('count');

        $topAttraction = AttractionCounter::whereDate('date', $today)
            ->selectRaw('attraction_id, SUM(count) as total')
            ->groupBy('attraction_id')
            ->orderByDesc('total')
            ->with('attraction')
            ->first();

        [$totalDesc, $totalIcon, $totalColor] = $this->getChangeInfo($totalToday, $totalYesterday);
        [$monthDesc, $monthIcon, $monthColor] = $this->getChangeInfo($totalMonth, $totalLastMonth);

        // ============================================================
        // SOLUSI N+1 chart: 1 query per chart
        // ============================================================
        $dailyAllChart = $this->getDailyChart(null, 7);
        $monthlyAllChart = $this->getMonthlyChart(null, 6);
        $topAttractionChart = $topAttraction
            ? $this->getDailyChart($topAttraction->attraction_id, 7)
            : [];

        return [
            Stat::make('Total Kunjungan Hari Ini', number_format($totalToday, 0, ',', '.'))
                ->description($totalDesc . ' · Minggu ini: ' . number_format($totalWeek, 0, ',', '.'))
                ->descriptionIcon($totalIcon)
                ->color($totalColor)
                ->chart($dailyAllChart),

            Stat::make('Wahana Terpopuler', $topAttraction?->attraction?->name ?? 'Belum ada data')
                ->description($topAttraction
                    ? number_format($topAttraction->total, 0, ',', '.') . ' kunjungan hari ini'
                    : 'Belum ada kunjungan hari ini')
                ->icon(Heroicon::OutlinedTrophy)
                ->descriptionIcon('heroicon-o-star')
                ->color('warning')
                ->chart($topAttractionChart),

            Stat::make('Kunjungan Bulan Ini', number_format($totalMonth, 0, ',', '.'))
                ->description($monthDesc . ' vs bulan lalu (' . number_format($totalLastMonth, 0, ',', '.') . ')')
                ->descriptionIcon($monthIcon)
                ->color($monthColor)
                ->chart($monthlyAllChart),
        ];
    }

    private function getChangeInfo(int $current, int $previous): array
    {
        if ($previous === 0 && $current === 0)
            return ['Belum ada kunjungan', 'heroicon-m-minus', 'gray'];
        if ($previous === 0 && $current > 0)
            return ['Kunjungan baru hari ini', 'heroicon-m-arrow-trending-up', 'success'];

        $change = round((($current - $previous) / $previous) * 100, 1);
        if ($change > 0)
            return [$change . '% kenaikan dari kemarin', 'heroicon-m-arrow-trending-up', 'success'];
        if ($change < 0)
            return [abs($change) . '% penurunan dari kemarin', 'heroicon-m-arrow-trending-down', 'danger'];
        return ['Sama dengan kemarin', 'heroicon-m-minus', 'info'];
    }

    // SOLUSI: 1 query untuk N hari, optional filter per wahana
    private function getDailyChart(?int $attractionId, int $days): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $query = AttractionCounter::query()
            ->selectRaw('date, SUM(count) as total')
            ->where('date', '>=', $start)
            ->groupBy('date');

        if ($attractionId) {
            $query->where('attraction_id', $attractionId);
        }

        $data = $query->pluck('total', 'date'); // ['2024-01-01' => 120, ...]

        return collect(range($days - 1, 0))
            ->map(fn($d) => (int) ($data[Carbon::today()->subDays($d)->toDateString()] ?? 0))
            ->toArray();
    }

    // SOLUSI: 1 query untuk N bulan, optional filter per wahana
    private function getMonthlyChart(?int $attractionId, int $months): array
    {
        $start = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $query = AttractionCounter::query()
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month, SUM(count) as total")
            ->where('date', '>=', $start)
            ->groupBy('month');

        if ($attractionId) {
            $query->where('attraction_id', $attractionId);
        }

        $data = $query->pluck('total', 'month'); // ['2024-01' => 3500, ...]

        return collect(range($months - 1, 0))
            ->map(fn($m) => (int) ($data[Carbon::now()->subMonths($m)->format('Y-m')] ?? 0))
            ->toArray();
    }
}