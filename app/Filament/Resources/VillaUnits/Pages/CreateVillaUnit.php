<?php

namespace App\Filament\Resources\VillaUnits\Pages;

use App\Filament\Resources\VillaUnits\VillaUnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVillaUnit extends CreateRecord
{
    protected static string $resource = VillaUnitResource::class;

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
        return 'Tambah Unit Villa';
    }
}
