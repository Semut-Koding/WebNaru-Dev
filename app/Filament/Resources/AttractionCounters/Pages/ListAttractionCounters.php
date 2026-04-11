<?php

namespace App\Filament\Resources\AttractionCounters\Pages;

use App\Filament\Resources\AttractionCounters\AttractionCounterResource;
use App\Filament\Resources\AttractionCounters\Widgets\AttractionCounterOverview;
use App\Filament\Resources\AttractionCounters\Widgets\AttractionCounterTableWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttractionCounters extends ListRecords
{
    protected static string $resource = AttractionCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengunjung Wahana'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AttractionCounterOverview::class,
            AttractionCounterTableWidget::class
        ];
    }

    public function getHeading(): string
    {
        return '';
    }
}
