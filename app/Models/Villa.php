<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable(['name', 'slug', 'description', 'bedroom_count', 'bathroom_count', 'capacity', 'amenities', 'benefits', 'base_price_weekday', 'base_price_weekend', 'status', 'sort_order', 'coordinate'])]
class Villa extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, LogsActivity;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    
    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'benefits' => 'array',
        ];
    }
    
    public function units()
    {
        return $this->hasMany(VillaUnit::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_image')
            ->useDisk('media')
            ->singleFile();

        $this->addMediaCollection('gallery_images')
            ->useDisk('media');
    }
}
