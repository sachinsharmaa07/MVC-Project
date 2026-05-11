<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Models\Route;
use App\Models\Truck;
use Illuminate\Support\Str;

class AnalyticsController extends Controller
{
    public function summary()
    {
        $dailyRequests = PickupRequest::raw(fn($collection) => $collection->aggregate([
            ['$match' => ['created_at' => ['$gte' => now()->subDays(30)]]],
            ['$group' => ['_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$created_at']], 'count' => ['$sum' => 1]]],
            ['$sort' => ['_id' => 1]],
        ]));

        $wasteTypes = PickupRequest::raw(fn($collection) => $collection->aggregate([
            ['$group' => ['_id' => '$waste_type', 'count' => ['$sum' => 1]]],
        ]));

        $zoneCompliance = $this->zoneCompliance();
        $truckUtilization = $this->truckUtilization();

        return response()->json([
            'daily_requests' => collect($dailyRequests)->map(fn($row) => ['date' => $row->_id, 'count' => $row->count])->values(),
            'waste_breakdown' => collect($wasteTypes)->map(fn($row) => ['type' => $row->_id, 'count' => $row->count])->values(),
            'zone_compliance' => $zoneCompliance,
            'truck_utilization' => $truckUtilization,
        ]);
    }

    private function zoneCompliance(): array
    {
        $requests = PickupRequest::select(['address', 'segregation_status'])->get();
        $zones = [];

        foreach ($requests as $request) {
            $zone = Str::of((string) $request->address)->before(',')->trim()->limit(40, '');
            $zoneKey = $zone !== '' ? (string) $zone : 'Unknown';

            if (!isset($zones[$zoneKey])) {
                $zones[$zoneKey] = ['total' => 0, 'compliant' => 0];
            }

            $zones[$zoneKey]['total'] += 1;
            if ($request->segregation_status === 'compliant') {
                $zones[$zoneKey]['compliant'] += 1;
            }
        }

        $results = [];
        foreach ($zones as $zone => $stats) {
            $rate = $stats['total'] > 0 ? round(($stats['compliant'] / $stats['total']) * 100, 2) : 0;
            $results[] = ['zone' => $zone, 'rate' => $rate];
        }

        return $results;
    }

    private function truckUtilization(): array
    {
        $routes = Route::all();
        $stats = [];

        foreach ($routes as $route) {
            $truckId = $route->truck_id;
            if (!$truckId) {
                continue;
            }

            if (!isset($stats[$truckId])) {
                $stats[$truckId] = ['total' => 0, 'completed' => 0];
            }

            $stats[$truckId]['total'] += 1;
            if ($route->status === 'completed') {
                $stats[$truckId]['completed'] += 1;
            }
        }

        $results = [];
        foreach ($stats as $truckId => $stat) {
            $truck = Truck::find($truckId);
            $label = $truck?->registration_number ?? (string) $truckId;
            $rate = $stat['total'] > 0 ? round(($stat['completed'] / $stat['total']) * 100, 2) : 0;
            $results[] = ['truck' => $label, 'rate' => $rate];
        }

        return $results;
    }
}
