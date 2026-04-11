<?php

namespace App\Filament\Resources\Villas\Pages;

use App\Filament\Resources\Villas\VillaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVilla extends CreateRecord
{
    protected static string $resource = VillaResource::class;

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
        return 'Tambah Data Villa';
    }
}
