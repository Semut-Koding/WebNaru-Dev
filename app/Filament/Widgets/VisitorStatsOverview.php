<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasPeriodFilter;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\VisitorCounter;
use App\Models\AttractionCounter;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class VisitorStatsOverview extends StatsOverviewWidget
{
    use HasWidgetShield, HasPeriodFilter, InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $range = $this->getDateRange();
        $label = $this->getPeriodLabel();

        $totalVisitors = VisitorCounter::whereBetween('date', [$range['start'], $range['end']])
            ->sum(DB::raw('adult_count + teenager_count + child_count'));

        $totalAttractions = AttractionCounter::whereBetween('date', [$range['start'], $range['end']])
            ->sum('count');

        $newReservations = Reservation::whereBetween('created_at', [$range['start'], $range['end']])
            ->count();

        return [
            Stat::make("Pengunjung ({$label})", $totalVisitors)
                ->description('Total pengunjung yang dicatat kasir')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make("Bermain Wahana ({$label})", $totalAttractions)
                ->description('Dari semua wahana')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),

            Stat::make("Reservasi Baru ({$label})", $newReservations)
                ->description('Booking Villa (baru / dp / paid)')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('warning'),
        ];
    }
}
