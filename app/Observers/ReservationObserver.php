<?php

namespace App\Observers;

use App\Models\Reservation;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     * Sync VillaUnit status when reservation is created with checked_in status.
     */
    public function created(Reservation $reservation): void
    {
        if ($reservation->status === 'checked_in') {
            $unit = $reservation->villaUnit;
            if ($unit) {
                $unit->update(['status' => 'occupied']);
            }
        }
    }

    /**
     * Handle the Reservation "updated" event.
     * Sync VillaUnit status based on reservation status changes.
     */
    public function updated(Reservation $reservation): void
    {
        if ($reservation->isDirty('status')) {
            $unit = $reservation->villaUnit;

            if (!$unit) return;

            match ($reservation->status) {
                'checked_in' => $unit->update(['status' => 'occupied']),
                'checked_out', 'cancelled' => $unit->update(['status' => 'available']),
                default => null,
            };
        }
    }
}
