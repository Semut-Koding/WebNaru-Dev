<?php

namespace App\Filament\Resources\VisitorCounters\Pages;

use App\Filament\Resources\VisitorCounters\VisitorCounterResource;
use App\Filament\Resources\VisitorCounters\Widgets\VisitorCounterOverview;
use App\Filament\Resources\VisitorCounters\Widgets\VisitorCounterTableWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVisitorCounters extends ListRecords
{
    protected static string $resource = VisitorCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengunjung Wisata'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VisitorCounterOverview::class,
            VisitorCounterTableWidget::class,
        ];
    }
    public function getHeading(): string
    {
        return '';
    }
}
