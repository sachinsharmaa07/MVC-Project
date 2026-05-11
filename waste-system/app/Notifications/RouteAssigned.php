<?php

namespace App\Notifications;

use App\Models\Route;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RouteAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Route $route)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'route_id' => (string) $this->route->id,
            'scheduled_date' => optional($this->route->scheduled_date)->toDateString(),
            'estimated_duration_minutes' => $this->route->estimated_duration_minutes,
            'stop_count' => is_array($this->route->stops) ? count($this->route->stops) : 0,
        ];
    }
}
