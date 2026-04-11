<?php

namespace App\Filament\Resources\VillaUnits\Pages;

use App\Filament\Resources\VillaUnits\VillaUnitResource;
use Filament\Resources\Pages\EditRecord;

class EditVillaUnit extends EditRecord
{
    protected static string $resource = VillaUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return 'Edit Unit Villa';
    }
}
