<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['villa_id', 'unit_name', 'status', 'is_active', 'notes'])]
class VillaUnit extends Model
{
    use HasFactory, SoftDeletes;
    
    public function villa()
    {
        return $this->belongsTo(Villa::class);
    }

    /**
     * Semua reservasi untuk unit ini.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Reservasi aktif saat ini (yang sedang checked_in).
     */
    public function activeReservation()
    {
        return $this->hasOne(Reservation::class)
            ->where('status', 'checked_in')
            ->latest('check_in_date');
    }

    /**
     * Reservasi mendatang terdekat (booked/pending, check_in di masa depan).
     */
    public function nextReservation()
    {
        return $this->hasOne(Reservation::class)
            ->whereIn('status', ['pending', 'booked'])
            ->where('check_in_date', '>', now())
            ->orderBy('check_in_date', 'asc');
    }
}
