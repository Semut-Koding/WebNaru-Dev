<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Filament\Resources\Reservations\Schemas\ReservationEditForm;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    public function form(Schema $schema): Schema
    {
        return ReservationEditForm::configure($schema);
    }

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

    // Judul di Tab Browser
    public function getTitle(): string
    {
        return 'Edit Reservasi';
    }
}
