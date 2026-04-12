<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable(['name', 'type', 'pricing_unit', 'price', 'description', 'is_active'])]
class Addon extends Model
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
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function addonOrders()
    {
        return $this->hasMany(VillaAddonOrder::class);
    }

    /**
     * Get human-readable pricing unit label.
     */
    public function getPricingUnitLabelAttribute(): string
    {
        return match ($this->pricing_unit) {
            'flat' => 'Per Order',
            'per_night' => 'Per Malam',
            'per_person' => 'Per Orang',
            'per_person_per_night' => 'Per Orang/Malam',
            default => $this->pricing_unit,
        };
    }
}
