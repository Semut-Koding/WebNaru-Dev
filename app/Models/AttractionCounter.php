<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable(['attraction_id', 'date', 'count', 'notes', 'attraction_operator_id'])]
class AttractionCounter extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function attraction()
    {
        return $this->belongsTo(Attraction::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'attraction_operator_id');
    }
}
