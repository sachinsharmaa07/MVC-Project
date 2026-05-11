<?php

namespace App\Jobs;

use App\Models\PickupRequest;
use App\Notifications\CollectionCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCollectionCompleteAlert implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $pickupRequestId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $pickupRequestId)
    {
        $this->pickupRequestId = $pickupRequestId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pickupRequest = PickupRequest::find($this->pickupRequestId);
        if (!$pickupRequest) {
            return;
        }

        $citizen = $pickupRequest->citizen;
        if ($citizen) {
            $citizen->notify(new CollectionCompleted($pickupRequest));
        }
    }
}
