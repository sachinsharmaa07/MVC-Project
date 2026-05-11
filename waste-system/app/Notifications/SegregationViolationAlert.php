<?php

namespace App\Notifications;

use App\Models\PickupRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SegregationViolationAlert extends Notification implements ShouldQueue
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
            ->subject('Segregation Violation Flagged')
            ->line('A pickup request has been flagged as non-compliant.')
            ->line('Request ID: '.(string) $this->pickupRequest->id)
            ->line('Address: '.$this->pickupRequest->address)
            ->action('Review Request', url('/admin/requests'));
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
            'segregation_status' => $this->pickupRequest->segregation_status,
            'address' => $this->pickupRequest->address,
        ];
    }
}
