<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function pickupRequests()
    {
        return $this->hasMany(PickupRequest::class, 'citizen_id');
    }

    public function truck()
    {
        return $this->hasOne(Truck::class, 'driver_id');
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles, true);
    }

    public function hasAnyRole(...$roles): bool
    {
        $roles = count($roles) === 1 && is_array($roles[0]) ? $roles[0] : $roles;

        return $this->hasRole($roles);
    }

    public function hasAllRoles($roles, ?string $guard = null): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return collect($roles)->every(fn ($role) => $this->hasRole($role));
    }
}
