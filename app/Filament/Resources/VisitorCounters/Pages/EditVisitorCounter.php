<?php

namespace App\Filament\Resources\VisitorCounters\Pages;

use App\Filament\Resources\VisitorCounters\VisitorCounterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVisitorCounter extends EditRecord
{
    protected static string $resource = VisitorCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

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
        return 'Edit Data Pengunjung Wisata';
    }
}
