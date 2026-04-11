<?php

namespace App\Filament\Resources\AttractionCounters\Pages;

use App\Filament\Resources\AttractionCounters\AttractionCounterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAttractionCounter extends EditRecord
{
    protected static string $resource = AttractionCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [];
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
        return 'Edit Data Pengunjung Wahana';
    }
}
