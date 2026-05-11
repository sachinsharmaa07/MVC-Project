<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Route extends Model
{
    /** @use HasFactory<\Database\Factories\RouteFactory> */
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'routes';

    protected $fillable = [
        'truck_id',
        'admin_id',
        'name',
        'description',
        'scheduled_date',
        'status',
        'estimated_duration_minutes',
        'stops',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'stops' => 'array',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
