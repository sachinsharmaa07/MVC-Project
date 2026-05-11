<?php

namespace App\Jobs;

use App\Models\PickupRequest;
use App\Models\User;
use App\Notifications\SegregationViolationAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FlagSegregationViolation implements ShouldQueue
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

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new SegregationViolationAlert($pickupRequest));
        }
    }
}
