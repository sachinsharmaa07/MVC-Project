@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">{{ $route->name }}</h1>
        <p class="text-sm text-slate-600">Scheduled {{ optional($route->scheduled_date)->format('d M Y') }} · Status {{ $route->status }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Route Map</h2>
            <div id="route-show-map" class="rounded-xl border border-slate-200"></div>
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
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #route-show-map {
            height: 420px;
        }
        .marker-label {
            background: #0f172a;
            color: #fff;
            border-radius: 999px;
            padding: 2px 6px;
            font-size: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const stops = @json(collect($route->stops)->sortBy('order_index')->values()->map(function ($stop) use ($requests) {
            $request = $requests->get($stop['pickup_request_id'] ?? '');
            return [
                'order' => $stop['order_index'] ?? null,
                'address' => $request?->address,
                'waste_type' => $request?->waste_type,
                'location' => $request?->location,
            ];
        }));

        const map = L.map('route-show-map').setView([28.6139, 77.2090], 11);
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
            const marker = L.marker([lat, lng]).addTo(map);
            marker.bindTooltip(`#${stop.order}`, { permanent: true, className: 'marker-label' });
            marker.bindPopup(`${stop.address || 'Stop'} (${stop.waste_type || 'unknown'})`);
            bounds.push([lat, lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [24, 24] });
        }
    </script>
@endpush
