<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class PickupRequest extends Model
{
    /** @use HasFactory<\Database\Factories\PickupRequestFactory> */
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'pickup_requests';

    protected $fillable = [
        'citizen_id',
        'location',
        'address',
        'waste_type',
        'segregation_status',
        'status',
        'scheduled_at',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'location' => 'array',
        'scheduled_at' => 'datetime',
    ];

    public function citizen()
    {
        return $this->belongsTo(User::class, 'citizen_id');
    }

    public function routes()
    {
        return $this->belongsToMany(Route::class, 'route_requests', 'pickup_request_id', 'route_id');
    }

    public function wasteLog()
    {
        return $this->hasOne(WasteLog::class, 'pickup_request_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByWasteType($query, $type)
    {
        return $query->where('waste_type', $type);
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('segregation_status', 'non_compliant');
    }

    public function scopeNearby($query, $lat, $lng, $meters)
    {
        return $query->where('location', 'near', [
            '$geometry' => ['type' => 'Point', 'coordinates' => [$lng, $lat]],
            '$maxDistance' => $meters,
        ]);
    }
}
