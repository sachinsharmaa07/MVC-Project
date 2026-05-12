<?php

namespace App\Providers;

use App\Models\PickupRequest;
use App\Policies\PickupRequestPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        PickupRequest::class => PickupRequestPolicy::class,
    ];

    public function boot(): void
    {
    }
}
