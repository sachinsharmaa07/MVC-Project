<?php

namespace App\Notifications;

use App\Models\PickupRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PickupRequestCreated extends Notification implements ShouldQueue
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
            ->subject('Pickup Request Received')
            ->line('Your pickup request has been received.')
            ->line('Waste type: '.$this->pickupRequest->waste_type)
            ->line('Address: '.$this->pickupRequest->address)
            ->action('Track Request', url('/citizen/requests/'.$this->pickupRequest->id))
            ->line('We will notify you once it is assigned.');
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
            'address' => $this->pickupRequest->address,
        ];
    }
}
