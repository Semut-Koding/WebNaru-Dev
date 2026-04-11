<?php

namespace App\Filament\Resources\Addons\Pages;

use App\Filament\Resources\Addons\AddonResource;
use App\Filament\Resources\Addons\Widgets\AddonOverviewWidget;
use App\Filament\Resources\Addons\Widgets\AddonTableWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAddons extends ListRecords
{
    protected static string $resource = AddonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Add-on'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AddonOverviewWidget::class,
            AddonTableWidget::class
        ];
    }

    public function getHeading(): string
    {
        return '';
    }
}
