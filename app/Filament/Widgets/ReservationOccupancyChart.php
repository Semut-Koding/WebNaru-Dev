<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasPeriodFilter;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\ChartWidget;
use App\Models\Reservation;
use App\Models\VillaUnit;
use Carbon\Carbon;

class ReservationOccupancyChart extends ChartWidget
{
    use HasWidgetShield, HasPeriodFilter, InteractsWithPageFilters;

    protected static ?int $sort = 4;

    public function getHeading(): ?string
    {
        return 'Occupancy Rate Villa (' . $this->getPeriodLabel() . ')';
    }

    protected function getData(): array
    {
        $range = $this->getDateRange();
        $start = Carbon::parse($range['start']);
        $end = Carbon::parse($range['end']);
        $totalDays = max(1, $start->diffInDays($end) + 1);

        $totalActiveUnits = VillaUnit::where('is_active', true)->count();
        $totalAvailableNights = max(1, $totalActiveUnits * $totalDays);

        // Calculate occupied nights from reservations
        $reservations = Reservation::whereIn('status', ['booked', 'checked_in', 'checked_out'])
            ->where('check_in_date', '<=', $range['end'])
            ->where('check_out_date', '>=', $range['start'])
            ->get(['check_in_date', 'check_out_date']);

        $occupiedNights = 0;
        foreach ($reservations as $reservation) {
            $resStart = Carbon::parse($reservation->check_in_date)->max($start);
            $resEnd = Carbon::parse($reservation->check_out_date)->min($end);
            $occupiedNights += max(0, $resStart->diffInDays($resEnd));
        }

        $occupancyRate = round(($occupiedNights / $totalAvailableNights) * 100, 1);
        $vacancyRate = round(100 - $occupancyRate, 1);

        return [
            'datasets' => [
                [
                    'label' => 'Tingkat Hunian',
                    'data' => [$occupancyRate, $vacancyRate],
                    'backgroundColor' => [
                        '#10b981', // emerald-500 (terisi)
                        '#e5e7eb', // gray-200 (kosong)
                    ],
                ],
            ],
            'labels' => ["Terisi ({$occupancyRate}%)", "Kosong ({$vacancyRate}%)"],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
