<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasPeriodFilter;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationAlosWidget extends StatsOverviewWidget
{
    use HasWidgetShield, HasPeriodFilter, InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $range = $this->getDateRange();

        $reservations = Reservation::whereIn('status', ['booked', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->get(['check_in_date', 'check_out_date']);

        $totalNights = 0;
        $totalReservations = $reservations->count();

        foreach ($reservations as $reservation) {
            $checkIn = Carbon::parse($reservation->check_in_date);
            $checkOut = Carbon::parse($reservation->check_out_date);
            $totalNights += max(0, $checkIn->diffInDays($checkOut));
        }

        $alos = $totalReservations > 0
            ? round($totalNights / $totalReservations, 1)
            : 0;

        // Calculate total revenue in period
        $totalRevenue = Reservation::whereIn('status', ['booked', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$range['start'], $range['end']])
            ->sum('total_price');

        $label = $this->getPeriodLabel();

        return [
            Stat::make("ALOS ({$label})", $alos . ' Malam')
                ->description('Average Length of Stay')
                ->descriptionIcon('heroicon-m-moon')
                ->color('primary'),

            Stat::make("Total Reservasi ({$label})", $totalReservations)
                ->description("{$totalNights} total malam menginap")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make("Revenue ({$label})", 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total pendapatan villa')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
