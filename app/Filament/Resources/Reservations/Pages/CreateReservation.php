<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Addon;
use App\Models\VillaAddonOrder;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove addon_orders from main data (handled in afterCreate)
        unset($data['addon_orders']);
        // Remove villa_id (not a column in reservations table)
        unset($data['villa_id']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $addonOrders = $this->data['addon_orders'] ?? [];

        foreach ($addonOrders as $order) {
            $addonId = $order['addon_id'] ?? null;
            if (!$addonId) continue;

            $addon = Addon::find($addonId);
            if (!$addon) continue;

            $qty = (int) ($order['quantity'] ?? 1);
            $nights = (int) ($order['nights'] ?? 1);
            $persons = (int) ($order['persons'] ?? 1);
            $subtotal = VillaAddonOrder::calculateSubtotal($addon, $qty, $nights, $persons);

            VillaAddonOrder::create([
                'reservation_id' => $this->record->id,
                'addon_id' => $addonId,
                'quantity' => $qty,
                'nights' => in_array($addon->pricing_unit, ['per_night', 'per_person_per_night']) ? $nights : null,
                'persons' => in_array($addon->pricing_unit, ['per_person', 'per_person_per_night']) ? $persons : null,
                'unit_price' => $addon->price,
                'subtotal' => $subtotal,
                'status' => 'pending',
                'notes' => $order['notes'] ?? null,
            ]);
        }
    }

    // Menghilangkan judul di bagian header halaman
    public function getHeading(): string
    {
        return '';
    }

    // Jika Anda juga ingin menghilangkan judul di Tab Browser/Breadcrumb
    public function getTitle(): string
    {
        return 'Tambah Data Reservasi';
    }
}
