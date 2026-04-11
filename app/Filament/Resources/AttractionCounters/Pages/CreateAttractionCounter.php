<?php

namespace App\Filament\Resources\AttractionCounters\Pages;

use App\Filament\Resources\AttractionCounters\AttractionCounterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttractionCounter extends CreateRecord
{
    protected static string $resource = AttractionCounterResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        session(['last_attraction_id' => $data['attraction_id'] ?? null]);
        return $data;
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
        return 'Tambah Data Pengunjung Wahana';
    }
}
