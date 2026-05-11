<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\NotifyDriverOfNewRoute;
use App\Models\PickupRequest;
use App\Models\Route;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::orderBy('scheduled_date', 'desc')->get();

        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        $trucks = Truck::available()->get();
        $requests = PickupRequest::pending()->get();

        return view('admin.routes.create', compact('trucks', 'requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'truck_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'scheduled_date' => ['required', 'date'],
            'estimated_duration_minutes' => ['required', 'integer', 'min:10'],
            'stops' => ['required', 'array'],
            'stops.*' => ['nullable', 'integer', 'min:1'],
        ]);

        $stops = collect($validated['stops'])
            ->filter()
            ->sort()
            ->map(function ($order, $requestId) {
                return [
                    'pickup_request_id' => (string) $requestId,
                    'order_index' => (int) $order,
                    'completed_at' => null,
                ];
            })
            ->values()
            ->all();

        if (count($stops) === 0) {
            return back()->withErrors(['stops' => 'Please add at least one stop.']);
        }

        $route = Route::create([
            'truck_id' => $validated['truck_id'],
            'admin_id' => (string) auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'scheduled_date' => Carbon::parse($validated['scheduled_date'])->startOfDay(),
            'status' => 'planned',
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'],
            'stops' => $stops,
        ]);

        $pickupIds = collect($stops)->pluck('pickup_request_id')->all();
        PickupRequest::whereIn('_id', $pickupIds)->update(['status' => 'assigned']);
        Truck::where('_id', $validated['truck_id'])->update(['status' => 'on_route']);

        NotifyDriverOfNewRoute::dispatch((string) $route->id);

        return redirect()->route('admin.routes.show', $route->id)->with('status', 'Route created.');
    }

    public function show(string $id)
    {
        $route = Route::findOrFail($id);
        $pickupIds = collect($route->stops)->pluck('pickup_request_id')->all();
        $requests = PickupRequest::whereIn('_id', $pickupIds)->get()->keyBy('_id');

        return view('admin.routes.show', compact('route', 'requests'));
    }
}
