<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Truck extends Model
{
    /** @use HasFactory<\Database\Factories\TruckFactory> */
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'trucks';

    protected $fillable = [
        'registration_number',
        'capacity_kg',
        'waste_types_supported',
        'driver_id',
        'status',
        'current_location',
    ];

    protected $casts = [
        'waste_types_supported' => 'array',
        'current_location' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function routes()
    {
        return $this->hasMany(Route::class, 'truck_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeSupportsWasteType($query, $type)
    {
        return $query->where('waste_types_supported', $type);
    }
}
