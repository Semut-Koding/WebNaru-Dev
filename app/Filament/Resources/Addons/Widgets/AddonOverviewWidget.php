<?php

namespace App\Filament\Resources\Addons\Widgets;

use App\Models\VillaAddonOrder;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AddonOverviewWidget extends StatsOverviewWidget
{

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Stat queries — tetap sama, sudah efisien (agregat tunggal)
        $todayOrders = VillaAddonOrder::whereDate('created_at', $today)->count();
        $yesterdayOrders = VillaAddonOrder::whereDate('created_at', $yesterday)->count();
        $todayRevenue = VillaAddonOrder::whereDate('created_at', $today)->sum('subtotal');
        $revenueThisMonth = VillaAddonOrder::whereBetween('created_at', [$thisMonthStart, Carbon::now()])->sum('subtotal');
        $revenueLastMonth = VillaAddonOrder::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('subtotal');

        $topToday = VillaAddonOrder::whereDate('created_at', $today)
            ->selectRaw('addon_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('addon_id')->orderByDesc('total_qty')->with('addon')->first();

        $topMonth = VillaAddonOrder::whereBetween('created_at', [$thisMonthStart, Carbon::now()])
            ->selectRaw('addon_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('addon_id')->orderByDesc('total_qty')->with('addon')->first();

        [$orderDesc, $orderIcon, $orderColor] = $this->getChangeInfo($todayOrders, $yesterdayOrders);
        [$revDesc, $revIcon, $revColor] = $this->getChangeInfo((int) $revenueThisMonth, (int) $revenueLastMonth);

        // ============================================================
        // SOLUSI N+1 chart: 1 query per chart, group by tanggal/bulan
        // ============================================================
        $dailyOrderChart = $this->getDailyChart(7);
        $monthlyRevenueChart = $this->getMonthlyRevenueChart(6);
        $topTodayChart = $topToday ? $this->getDailyChartByAddon($topToday->addon_id, 7) : [];
        $topMonthChart = $topMonth ? $this->getDailyChartByAddon($topMonth->addon_id, 7) : [];

        return [
            Stat::make('Order Addon Hari Ini', $todayOrders . ' Order')
                ->description($orderDesc . ' · Pendapatan: Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->descriptionIcon($orderIcon)
                ->color($orderColor)
                ->chart($dailyOrderChart),

            Stat::make('Terlaris Hari Ini', $topToday?->addon?->name ?? 'Belum ada order')
                ->description($topToday
                    ? $topToday->total_qty . 'x dipesan · Rp ' . number_format($topToday->total_revenue, 0, ',', '.')
                    : 'Belum ada transaksi hari ini')
                ->icon(Heroicon::OutlinedFire)
                ->descriptionIcon(Heroicon::OutlinedFire)
                ->color('warning')
                ->chart($topTodayChart),

            Stat::make('Terlaris Bulan Ini', $topMonth?->addon?->name ?? 'Belum ada order')
                ->description($topMonth
                    ? $topMonth->total_qty . 'x dipesan · Rp ' . number_format($topMonth->total_revenue, 0, ',', '.')
                    : 'Belum ada transaksi bulan ini')
                ->icon(Heroicon::OutlinedPuzzlePiece)
                ->descriptionIcon(Heroicon::OutlinedTrophy)
                ->color('success')
                ->chart($topMonthChart),

            Stat::make('Pendapatan Addon Bulan Ini', 'Rp ' . number_format($revenueThisMonth, 0, ',', '.'))
                ->description($revDesc . ' vs bulan lalu (Rp ' . number_format($revenueLastMonth, 0, ',', '.') . ')')
                ->descriptionIcon($revIcon)
                ->color($revColor)
                ->chart($monthlyRevenueChart),
        ];
    }

    private function getChangeInfo(int $current, int $previous): array
    {
        if ($previous === 0 && $current === 0)
            return ['Belum ada data', 'heroicon-m-minus', 'gray'];
        if ($previous === 0 && $current > 0)
            return ['Ada peningkatan baru', 'heroicon-m-arrow-trending-up', 'success'];

        $change = round((($current - $previous) / $previous) * 100, 1);
        if ($change > 0)
            return [$change . '% kenaikan', 'heroicon-m-arrow-trending-up', 'success'];
        if ($change < 0)
            return [abs($change) . '% penurunan', 'heroicon-m-arrow-trending-down', 'danger'];
        return ['Sama dengan sebelumnya', 'heroicon-m-minus', 'info'];
    }

    // SOLUSI: 1 query untuk 7 hari sekaligus, group by date
    private function getDailyChart(int $days): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $data = VillaAddonOrder::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', $start)
            ->groupBy('date')
            ->pluck('total', 'date'); // ['2024-01-01' => 5, ...]

        return collect(range($days - 1, 0))
            ->map(fn($d) => (int) ($data[Carbon::today()->subDays($d)->toDateString()] ?? 0))
            ->toArray();
    }

    // SOLUSI: 1 query untuk 7 hari per addon sekaligus
    private function getDailyChartByAddon(int $addonId, int $days): array
    {
        $start = Carbon::today()->subDays($days - 1);

        $data = VillaAddonOrder::query()
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
            ->where('addon_id', $addonId)
            ->where('created_at', '>=', $start)
            ->groupBy('date')
            ->pluck('total', 'date');

        return collect(range($days - 1, 0))
            ->map(fn($d) => (int) ($data[Carbon::today()->subDays($d)->toDateString()] ?? 0))
            ->toArray();
    }

    // SOLUSI: 1 query untuk 6 bulan sekaligus, group by year-month
    private function getMonthlyRevenueChart(int $months): array
    {
        $start = Carbon::now()->subMonths($months - 1)->startOfMonth();

        $data = VillaAddonOrder::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(subtotal) as total")
            ->where('created_at', '>=', $start)
            ->groupBy('month')
            ->pluck('total', 'month'); // ['2024-01' => 500000, ...]

        return collect(range($months - 1, 0))
            ->map(fn($m) => (int) ($data[Carbon::now()->subMonths($m)->format('Y-m')] ?? 0))
            ->toArray();
    }
}