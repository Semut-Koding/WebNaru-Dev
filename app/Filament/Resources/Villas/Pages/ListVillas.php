<?php

namespace App\Filament\Resources\Villas\Pages;

use App\Filament\Resources\Villas\VillaResource;
use App\Models\Villa;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVillas extends ListRecords
{
    protected static string $resource = VillaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Villa'),
        ];
    }

    public function reorderTable(array $order, string|int|null $draggedRecordKey = null): void
    {
        foreach ($order as $index => $id) {
            Villa::withTrashed()
                ->where('id', $id)
                ->update(['sort_order' => $index + 1]);
        }
    }

    public function getHeading(): string
    {
        return '';
    }
}
