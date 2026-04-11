<?php

namespace App\Filament\Resources\Attractions\Pages;

use App\Filament\Resources\Attractions\AttractionResource;
use App\Models\Attraction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAttractions extends ListRecords
{
    protected static string $resource = AttractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Wahana'),
        ];
    }

    public function reorderTable(array $order, int|string|null $draggedRecordKey = null): void
    {
        foreach ($order as $index => $id) {
            Attraction::withTrashed()
                ->where('id', $id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function getHeading(): string
    {
        return '';
    }
}
