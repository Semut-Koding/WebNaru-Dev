<?php

namespace App\Filament\Resources\Attractions\Pages;

use App\Filament\Resources\Attractions\AttractionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAttraction extends EditRecord
{
    protected static string $resource = AttractionResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    protected function getFormActions(): array
    {
        return [
        ];
    }

    // Menghilangkan judul di bagian header halaman
    public function getHeading(): string
    {
        return '';
    }

    // Jika Anda juga ingin menghilangkan judul di Tab Browser/Breadcrumb
    public function getTitle(): string
    {
        return 'Edit Data Wahana';
    }
}
