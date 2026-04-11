<?php

namespace App\Filament\Resources\VillaUnits\Pages;

use App\Filament\Resources\VillaUnits\VillaUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVillaUnits extends ListRecords
{
    protected static string $resource = VillaUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Unit Villa'),
        ];
    }

    public function getHeading(): string
    {
        return '';
    }
}
