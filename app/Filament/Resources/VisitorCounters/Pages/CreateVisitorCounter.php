<?php

namespace App\Filament\Resources\VisitorCounters\Pages;

use App\Filament\Resources\VisitorCounters\VisitorCounterResource;
use Filament\Resources\Pages\CreateRecord;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Size;

class CreateVisitorCounter extends CreateRecord
{
    protected static string $resource = VisitorCounterResource::class;

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
        return 'Tambah Data Pengunjung';
    }
}
