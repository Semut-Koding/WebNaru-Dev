<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Filament\Resources\Reservations\Schemas\ReservationEditForm;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    /**
     * Suppress default Filament save notification.
     * Custom notification is handled in ReservationEditForm's save action.
     */
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

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
