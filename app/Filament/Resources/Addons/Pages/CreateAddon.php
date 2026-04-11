<?php

namespace App\Filament\Resources\Addons\Pages;

use App\Filament\Resources\Addons\AddonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAddon extends CreateRecord
{
    protected static string $resource = AddonResource::class;

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
        return 'Tambah Data Addon';
    }
}
