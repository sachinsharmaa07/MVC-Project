<?php

namespace App\Notifications;

use App\Models\PickupRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectionCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PickupRequest $pickupRequest)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Collection Completed')
            ->line('Your waste collection is marked as completed.')
            ->line('Waste type: '.$this->pickupRequest->waste_type)
            ->action('View Details', url('/citizen/requests/'.$this->pickupRequest->id))
            ->line('Thank you for keeping your neighborhood clean.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'pickup_request_id' => (string) $this->pickupRequest->id,
            'status' => $this->pickupRequest->status,
            'waste_type' => $this->pickupRequest->waste_type,
        ];
    }
}
