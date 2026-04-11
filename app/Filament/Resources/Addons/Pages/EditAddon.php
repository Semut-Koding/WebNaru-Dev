<?php

namespace App\Filament\Resources\Addons\Pages;

use App\Filament\Resources\Addons\AddonResource;
use Filament\Resources\Pages\EditRecord;

class EditAddon extends EditRecord
{
    protected static string $resource = AddonResource::class;

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
        return 'Edit Data Addon';
    }
}
