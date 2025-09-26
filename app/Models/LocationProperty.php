<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationProperty extends Model
{
    use HasFactory;

    /** @var array<int, string> */
    protected $fillable = [
        'location_id',
        'iptu_registration',
        'built_area_sqm',
        'land_area_sqm',
        'tenure_type',
        'capacity',
        'floors',
        'activity_floor',
        'property_use',
        'property_section',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'built_area_sqm' => 'decimal:2',
        'land_area_sqm' => 'decimal:2',
        'capacity' => 'integer',
        'floors' => 'integer',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
