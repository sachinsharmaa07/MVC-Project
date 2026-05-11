<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class WasteLog extends Model
{
    /** @use HasFactory<\Database\Factories\WasteLogFactory> */
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'waste_logs';

    protected $fillable = [
        'pickup_request_id',
        'collected_weight_kg',
        'segregation_compliant',
        'collected_at',
        'driver_notes',
    ];

    protected $casts = [
        'collected_weight_kg' => 'decimal:2',
        'segregation_compliant' => 'boolean',
        'collected_at' => 'datetime',
    ];

    public function pickupRequest()
    {
        return $this->belongsTo(PickupRequest::class, 'pickup_request_id');
    }
}
