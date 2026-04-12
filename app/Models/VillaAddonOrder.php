<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable(['reservation_id', 'addon_id', 'quantity', 'nights', 'persons', 'unit_price', 'subtotal', 'status', 'notes'])]
class VillaAddonOrder extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function addon()
    {
        return $this->belongsTo(Addon::class);
    }

    /**
     * Calculate subtotal based on addon pricing_unit.
     */
    public static function calculateSubtotal(Addon $addon, int $quantity, ?int $nights = null, ?int $persons = null): float
    {
        return match ($addon->pricing_unit) {
            'flat' => $quantity * $addon->price,
            'per_night' => $quantity * ($nights ?? 1) * $addon->price,
            'per_person' => ($persons ?? 1) * $quantity * $addon->price,
            'per_person_per_night' => ($persons ?? 1) * ($nights ?? 1) * $addon->price,
            default => $quantity * $addon->price,
        };
    }
}
