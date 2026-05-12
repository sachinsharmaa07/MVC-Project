<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Models\Route;
use App\Models\Truck;
use Illuminate\Http\Request;

class PickupRequestController extends Controller
{
    public function index(Request $request)
    {
        $pickupRequests = PickupRequest::where('citizen_id', (string) $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pr) {
                return [
                    'id' => (string) $pr->id,
                    'status' => $pr->status,
                    'segregation_status' => $pr->segregation_status,
                    'waste_type' => $pr->waste_type,
                    'address' => $pr->address,
                    'location' => $pr->location,
                    'scheduled_at' => optional($pr->scheduled_at)->toDateTimeString(),
                    'created_at' => $pr->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'data' => $pickupRequests,
            'count' => $pickupRequests->count(),
        ]);
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

    public function show(Request $request, string $id)
    {
        $pickupRequest = PickupRequest::findOrFail($id);

        if ($pickupRequest->citizen_id !== (string) $request->user()->id) {
            abort(403);
        }

        return response()->json([
            'id' => (string) $pickupRequest->id,
            'citizen_id' => $pickupRequest->citizen_id,
            'status' => $pickupRequest->status,
            'segregation_status' => $pickupRequest->segregation_status,
            'waste_type' => $pickupRequest->waste_type,
            'address' => $pickupRequest->address,
            'location' => $pickupRequest->location,
            'photo_path' => $pickupRequest->photo_path,
            'notes' => $pickupRequest->notes,
            'scheduled_at' => optional($pickupRequest->scheduled_at)->toDateTimeString(),
            'created_at' => $pickupRequest->created_at->toDateTimeString(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $pickupRequest = PickupRequest::findOrFail($id);

        if ($pickupRequest->citizen_id !== (string) $request->user()->id) {
            abort(403);
        }

        if ($pickupRequest->status !== 'pending') {
            return response()->json(['message' => 'Cannot update non-pending requests'], 422);
        }

        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'waste_type' => ['nullable', 'in:dry,wet,hazardous,mixed'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $updateData = [];

        if (isset($validated['address'])) {
            $updateData['address'] = $validated['address'];
        }

        if (isset($validated['latitude']) && isset($validated['longitude'])) {
            $updateData['location'] = [
                'type' => 'Point',
                'coordinates' => [(float) $validated['longitude'], (float) $validated['latitude']],
            ];
        }

        if (isset($validated['waste_type'])) {
            $updateData['waste_type'] = $validated['waste_type'];
        }

        if (isset($validated['notes'])) {
            $updateData['notes'] = $validated['notes'];
        }

        if (!empty($updateData)) {
            $pickupRequest->update($updateData);
        }

        return response()->json([
            'id' => (string) $pickupRequest->id,
            'status' => $pickupRequest->status,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $pickupRequest = PickupRequest::findOrFail($id);

        if ($pickupRequest->citizen_id !== (string) $request->user()->id) {
            abort(403);
        }

        if ($pickupRequest->status !== 'pending') {
            return response()->json(['message' => 'Cannot delete non-pending requests'], 422);
        }

        $pickupRequest->delete();

        return response()->json(['message' => 'Request deleted successfully']);
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

        $stops = collect($route->stops)->sortBy('order_index')->map(function ($stop) use ($requests) {
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
