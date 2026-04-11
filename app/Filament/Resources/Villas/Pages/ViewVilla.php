<?php

namespace App\Filament\Resources\Villas\Pages;

use App\Filament\Resources\Villas\VillaResource;
use Filament\Resources\Pages\ViewRecord;

class ViewVilla extends ViewRecord
{
    protected static string $resource = VillaResource::class;

    public function getHeading(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return 'Detail Villa: ' . $this->record->name;
    }
}
