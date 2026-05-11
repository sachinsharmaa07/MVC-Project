<?php

namespace App\Jobs;

use App\Models\Route;
use App\Notifications\RouteAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyDriverOfNewRoute implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $routeId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $routeId)
    {
        $this->routeId = $routeId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $route = Route::find($this->routeId);
        if (!$route) {
            return;
        }

        $driver = $route->truck?->driver;
        if ($driver) {
            $driver->notify(new RouteAssigned($route));
        }
    }
}
