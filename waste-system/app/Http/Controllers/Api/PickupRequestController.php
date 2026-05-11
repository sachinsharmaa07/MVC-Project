<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Models\Route;
use App\Models\Truck;
use Illuminate\Http\Request;

class PickupRequestController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Not implemented.']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['required', 'string', 'max:255'],
            'waste_type' => ['required', 'in:dry,wet,hazardous,mixed'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $pickupRequest = PickupRequest::create([
            'citizen_id' => (string) $request->user()->id,
            'location' => [
                'type' => 'Point',
                'coordinates' => [(float) $validated['longitude'], (float) $validated['latitude']],
            ],
            'address' => $validated['address'],
            'waste_type' => $validated['waste_type'],
            'segregation_status' => 'pending_review',
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'id' => (string) $pickupRequest->id,
            'status' => $pickupRequest->status,
        ], 201);
    }

    public function show(string $id)
    {
        return response()->json(['message' => 'Not implemented.']);
    }

    public function update(Request $request, string $id)
    {
        return response()->json(['message' => 'Not implemented.']);
    }

    public function destroy(string $id)
    {
        return response()->json(['message' => 'Not implemented.']);
    }

    public function status(string $id)
    {
        $pickupRequest = PickupRequest::findOrFail($id);

        return response()->json([
            'id' => (string) $pickupRequest->id,
            'status' => $pickupRequest->status,
            'segregation_status' => $pickupRequest->segregation_status,
            'scheduled_at' => optional($pickupRequest->scheduled_at)->toDateTimeString(),
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,assigned,in_progress,completed,cancelled'],
            'segregation_status' => ['nullable', 'in:compliant,non_compliant,pending_review'],
        ]);

        $pickupRequest = PickupRequest::findOrFail($id);
        $pickupRequest->update(array_filter($validated, fn($value) => $value !== null));

        return response()->json([
            'id' => (string) $pickupRequest->id,
            'status' => $pickupRequest->status,
            'segregation_status' => $pickupRequest->segregation_status,
        ]);
    }

    public function routeStops(string $id)
    {
        $route = Route::findOrFail($id);
        $pickupIds = collect($route->stops)->pluck('pickup_request_id')->all();
        $requests = PickupRequest::whereIn('_id', $pickupIds)->get()->keyBy('_id');

        $stops = collect($route->stops)->map(function ($stop) use ($requests) {
            $request = $requests->get($stop['pickup_request_id'] ?? '');

            return [
                'pickup_request_id' => $stop['pickup_request_id'] ?? null,
                'order_index' => $stop['order_index'] ?? null,
                'completed_at' => $stop['completed_at'] ?? null,
                'address' => $request?->address,
                'waste_type' => $request?->waste_type,
                'location' => $request?->location,
            ];
        });

        return response()->json([
            'route_id' => (string) $route->id,
            'stops' => $stops->values()->all(),
        ]);
    }

    public function truckLocation(string $id)
    {
        $truck = Truck::findOrFail($id);

        return response()->json([
            'truck_id' => (string) $truck->id,
            'current_location' => $truck->current_location,
            'status' => $truck->status,
        ]);
    }
}
