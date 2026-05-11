<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupRequest;
use App\Models\Route;
use App\Models\Truck;
use App\Models\WasteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = $this->buildMetrics();
        $charts = $this->buildCharts();

        return view('admin.dashboard', compact('metrics', 'charts'));
    }

    public function requests(Request $request)
    {
        $query = PickupRequest::query();

        if ($request->filled('waste_type')) {
            $query->where('waste_type', $request->string('waste_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date')) {
            $date = Carbon::parse($request->string('date'))->startOfDay();
            $query->whereBetween('created_at', [$date, (clone $date)->endOfDay()]);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return view('admin.requests.index', compact('requests'));
    }

    public function analytics()
    {
        $charts = $this->buildCharts();

        return view('admin.analytics.index', compact('charts'));
    }

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'waste-collection-report.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Request ID',
                'Citizen ID',
                'Waste Type',
                'Segregation Status',
                'Status',
                'Address',
                'Created At',
            ]);

            PickupRequest::orderBy('created_at', 'desc')->chunk(200, function ($requests) use ($handle) {
                foreach ($requests as $request) {
                    fputcsv($handle, [
                        (string) $request->id,
                        $request->citizen_id,
                        $request->waste_type,
                        $request->segregation_status,
                        $request->status,
                        $request->address,
                        optional($request->created_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        }, $fileName);
    }

    private function buildMetrics(): array
    {
        $weekStart = now()->startOfWeek();
        $monthStart = now()->startOfMonth();

        $totalWeek = PickupRequest::where('created_at', '>=', $weekStart)->count();
        $totalMonth = PickupRequest::where('created_at', '>=', $monthStart)->count();
        $totalAll = PickupRequest::count();
        $compliantCount = PickupRequest::where('segregation_status', 'compliant')->count();
        $complianceRate = $totalAll > 0 ? round(($compliantCount / $totalAll) * 100, 2) : 0;

        $logs = WasteLog::whereNotNull('collected_at')->get();
        $avgCollectionHours = 0;

        if ($logs->count() > 0) {
            $totalSeconds = 0;
            foreach ($logs as $log) {
                $request = PickupRequest::find($log->pickup_request_id);
                if ($request && $request->created_at && $log->collected_at) {
                    $totalSeconds += $log->collected_at->diffInSeconds($request->created_at);
                }
            }

            $avgCollectionHours = $totalSeconds > 0 ? round(($totalSeconds / $logs->count()) / 3600, 2) : 0;
        }

        $activeTrucks = Truck::where('status', 'on_route')->count();

        return [
            'total_week' => $totalWeek,
            'total_month' => $totalMonth,
            'compliance_rate' => $complianceRate,
            'avg_collection_hours' => $avgCollectionHours,
            'active_trucks' => $activeTrucks,
        ];
    }

    private function buildCharts(): array
    {
        $dailyRequests = PickupRequest::raw(fn($collection) => $collection->aggregate([
            ['$match' => ['created_at' => ['$gte' => now()->subDays(30)]]],
            ['$group' => ['_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$created_at']], 'count' => ['$sum' => 1]]],
            ['$sort' => ['_id' => 1]],
        ]));

        $daily = collect($dailyRequests)->map(fn($row) => ['date' => $row->_id, 'count' => $row->count]);

        $wasteTypes = PickupRequest::raw(fn($collection) => $collection->aggregate([
            ['$group' => ['_id' => '$waste_type', 'count' => ['$sum' => 1]]],
        ]));

        $wasteBreakdown = collect($wasteTypes)->map(fn($row) => ['type' => $row->_id, 'count' => $row->count]);

        $zoneStats = $this->buildZoneCompliance();
        $truckUtilization = $this->buildTruckUtilization();

        return [
            'daily_requests' => $daily->values()->all(),
            'waste_breakdown' => $wasteBreakdown->values()->all(),
            'zone_compliance' => $zoneStats,
            'truck_utilization' => $truckUtilization,
        ];
    }

    private function buildZoneCompliance(): array
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

    private function buildTruckUtilization(): array
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
