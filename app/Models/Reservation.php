<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable(['booking_code', 'villa_unit_id', 'guest_name', 'guest_phone', 'guest_email', 'check_in_date', 'check_out_date', 'total_guests', 'total_price', 'dp_amount', 'paid_amount', 'payment_method', 'payment_status', 'status', 'source', 'notes', 'created_by'])]
class Reservation extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function villaUnit()
    {
        return $this->belongsTo(VillaUnit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function addonOrders()
    {
        return $this->hasMany(VillaAddonOrder::class);
    }

    /**
     * Get total add-ons cost.
     */
    public function getAddonsTotalAttribute(): float
    {
        return $this->addonOrders()->sum('subtotal');
    }
}
