@extends('layouts.driver')

@section('content')
    @if (isset($route))
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Route {{ $route->name }}</h1>
            <p class="text-sm text-slate-600">Stops ordered by priority. Tap a stop to log collection.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Stop Map</h2>
                <div id="driver-map" class="rounded-xl border border-slate-200"></div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Stops</h2>
                <ol class="space-y-3">
                    @foreach (collect($route->stops)->sortBy('order_index') as $stop)
                        @php $request = $requests->get($stop['pickup_request_id'] ?? '') @endphp
                        <li class="text-sm">
                            <div class="font-medium">Stop {{ $stop['order_index'] ?? '-' }}</div>
                            <div class="text-slate-600">{{ $request?->address }}</div>
                            <div class="text-xs text-slate-400 capitalize">{{ $request?->waste_type }}</div>
                            @if ($request)
                                <a class="text-xs text-blue-600" href="{{ route('driver.collect.form', $request->id) }}">Log collection</a>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @else
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Today's Routes</h1>
            <p class="text-sm text-slate-600">Select a route to see stops and navigation map.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="text-left px-4 py-3">Route</th>
                        <th class="text-left px-4 py-3">Scheduled</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">Stops</th>
                        <th class="text-left px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($routes as $route)
                        <tr>
                            <td class="px-4 py-3">{{ $route->name }}</td>
                            <td class="px-4 py-3">{{ optional($route->scheduled_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 capitalize">{{ $route->status }}</td>
                            <td class="px-4 py-3">{{ is_array($route->stops) ? count($route->stops) : 0 }}</td>
                            <td class="px-4 py-3 text-right">
                                <a class="text-blue-600" href="{{ route('driver.routes.stops', $route->id) }}">View Stops</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-slate-500" colspan="5">No routes assigned today.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #driver-map {
            height: 420px;
        }
        .waste-pin {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            border: 2px solid #0f172a;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @if (isset($route))
        <script>
            const stops = {!! json_encode(collect($route->stops)->sortBy('order_index')->values()->map(function ($stop) use ($requests) {
                $request = $requests->get($stop['pickup_request_id'] ?? '');
                return [
                    'order' => $stop['order_index'] ?? null,
                    'address' => $request?->address,
                    'waste_type' => $request?->waste_type,
                    'location' => $request?->location,
                ];
            })) !!};

            const colors = {
                dry: '#16a34a',
                wet: '#0ea5e9',
                hazardous: '#ef4444',
                mixed: '#f97316'
            };

            const map = L.map('driver-map').setView([28.6139, 77.2090], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);
            const bounds = [];

            stops.forEach(stop => {
                if (!stop.location || !stop.location.coordinates) {
                    return;
                }
                const lat = stop.location.coordinates[1];
                const lng = stop.location.coordinates[0];
                const color = colors[stop.waste_type] || '#64748b';
                const icon = L.divIcon({
                    className: 'waste-pin',
                    html: `<span class="waste-pin" style="background:${color}"></span>`
                });
                const marker = L.marker([lat, lng], { icon }).addTo(map);
                marker.bindPopup(`${stop.address || 'Stop'} (${stop.waste_type || 'unknown'})`);
                bounds.push([lat, lng]);
            });

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [24, 24] });
            }
        </script>
    @endif
@endpush
