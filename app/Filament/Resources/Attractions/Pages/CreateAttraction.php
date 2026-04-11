<?php

namespace App\Filament\Resources\Attractions\Pages;

use App\Filament\Resources\Attractions\AttractionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttraction extends CreateRecord
{
    protected static string $resource = AttractionResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    // Menghilangkan judul di bagian header halaman
    public function getHeading(): string
    {
        return '';
    }

    // Jika Anda juga ingin menghilangkan judul di Tab Browser/Breadcrumb
    public function getTitle(): string
    {
        return 'Tambah Data Wahana';
    }
}
