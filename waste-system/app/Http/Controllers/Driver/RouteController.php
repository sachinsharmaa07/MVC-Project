<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Jobs\FlagSegregationViolation;
use App\Jobs\SendCollectionCompleteAlert;
use App\Models\PickupRequest;
use App\Models\Route;
use App\Models\Truck;
use App\Models\WasteLog;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function today()
    {
        $truck = Truck::where('driver_id', (string) auth()->id())->first();

        if (!$truck) {
            return view('driver.route', ['routes' => collect(), 'truck' => null]);
        }

        $routes = Route::where('truck_id', (string) $truck->id)
            ->whereDate('scheduled_date', now()->toDateString())
            ->orderBy('scheduled_date', 'asc')
            ->get();

        return view('driver.route', compact('routes', 'truck'));
    }

    public function stops(string $id)
    {
        $route = Route::findOrFail($id);
        $truck = Truck::find($route->truck_id);
        if (!$truck || $truck->driver_id !== (string) auth()->id()) {
            abort(403);
        }
        $pickupIds = collect($route->stops)->pluck('pickup_request_id')->all();
        $requests = PickupRequest::whereIn('_id', $pickupIds)->get()->keyBy('_id');

        return view('driver.route', compact('route', 'requests'));
    }

    public function collect(Request $request, string $requestId)
    {
        $validated = $request->validate([
            'collected_weight_kg' => ['required', 'numeric', 'min:0.1'],
            'segregation_compliant' => ['required', 'boolean'],
            'driver_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $pickupRequest = PickupRequest::findOrFail($requestId);
        $route = Route::where('stops.pickup_request_id', (string) $pickupRequest->id)->first();
        if ($route) {
            $truck = Truck::find($route->truck_id);
            if (!$truck || $truck->driver_id !== (string) auth()->id()) {
                abort(403);
            }
        }

        WasteLog::create([
            'pickup_request_id' => (string) $pickupRequest->id,
            'collected_weight_kg' => $validated['collected_weight_kg'],
            'segregation_compliant' => (bool) $validated['segregation_compliant'],
            'collected_at' => now(),
            'driver_notes' => $validated['driver_notes'],
        ]);

        $pickupRequest->update([
            'status' => 'completed',
            'segregation_status' => $validated['segregation_compliant'] ? 'compliant' : 'non_compliant',
        ]);

        if ($route) {
            $stops = collect($route->stops)->map(function ($stop) use ($pickupRequest) {
                if (($stop['pickup_request_id'] ?? '') === (string) $pickupRequest->id) {
                    $stop['completed_at'] = now();
                }

                return $stop;
            });

            $route->stops = $stops->values()->all();
            if ($stops->every(fn($stop) => !empty($stop['completed_at']))) {
                $route->status = 'completed';
                Truck::where('_id', $route->truck_id)->update(['status' => 'available']);
            } else {
                $route->status = 'active';
            }
            $route->save();
        }

        SendCollectionCompleteAlert::dispatch((string) $pickupRequest->id);

        if (!$validated['segregation_compliant']) {
            FlagSegregationViolation::dispatch((string) $pickupRequest->id);
        }

        return back()->with('status', 'Collection logged.');
    }

    public function collectForm(string $requestId)
    {
        $pickupRequest = PickupRequest::findOrFail($requestId);
        $route = Route::where('stops.pickup_request_id', (string) $pickupRequest->id)->first();
        if ($route) {
            $truck = Truck::find($route->truck_id);
            if (!$truck || $truck->driver_id !== (string) auth()->id()) {
                abort(403);
            }
        }

        return view('driver.collect', compact('pickupRequest'));
    }
}
