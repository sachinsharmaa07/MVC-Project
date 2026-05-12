@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Admin Dashboard</h1>
        <p class="text-sm text-slate-600">Operational overview of requests, compliance, and fleet activity.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Requests This Week</p>
            <p class="text-2xl font-semibold mt-2">{{ $metrics['total_week'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Requests This Month</p>
            <p class="text-2xl font-semibold mt-2">{{ $metrics['total_month'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Compliance Rate</p>
            <p class="text-2xl font-semibold mt-2">{{ $metrics['compliance_rate'] }}%</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Avg Collection Time</p>
            <p class="text-2xl font-semibold mt-2">{{ $metrics['avg_collection_hours'] }} hrs</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-4">
            <p class="text-xs uppercase tracking-wide text-slate-500">Active Trucks</p>
            <p class="text-2xl font-semibold mt-2">{{ $metrics['active_trucks'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Daily Requests</h2>
            <canvas id="daily-requests-chart" height="140"></canvas>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Waste Type Breakdown</h2>
            <canvas id="waste-type-chart" height="140"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Live Fleet Map</h2>
        <div id="fleet-map" class="rounded-xl border border-slate-200"></div>
        <p class="text-xs text-slate-500 mt-2">Tracking {{ $activeTrucks->count() }} active trucks. Updates every 30 seconds.</p>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #fleet-map {
            height: 360px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const dailyData = @json($charts['daily_requests']);
        const wasteData = @json($charts['waste_breakdown']);

        const dailyCtx = document.getElementById('daily-requests-chart');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date),
                datasets: [{
                    label: 'Requests',
                    data: dailyData.map(item => item.count),
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const wasteCtx = document.getElementById('waste-type-chart');
        new Chart(wasteCtx, {
            type: 'doughnut',
            data: {
                labels: wasteData.map(item => item.type),
                datasets: [{
                    data: wasteData.map(item => item.count),
                    backgroundColor: ['#22c55e', '#0ea5e9', '#f97316', '#ef4444']
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });

        const fleetMap = L.map('fleet-map').setView([28.6139, 77.2090], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(fleetMap);

        const trucks = @json($activeTrucks->map(fn($truck) => [
            'id' => (string) $truck->id,
            'name' => $truck->registration_number,
            'location' => $truck->current_location,
        ])->values());

        const markers = {};
        const bounds = [];

        const upsertMarker = (truckId, name, location) => {
            if (!location || !location.coordinates) {
                return;
            }
            const lat = location.coordinates[1];
            const lng = location.coordinates[0];
            if (markers[truckId]) {
                markers[truckId].setLatLng([lat, lng]);
                return;
            }
            markers[truckId] = L.marker([lat, lng]).addTo(fleetMap).bindPopup(name || 'Truck');
            bounds.push([lat, lng]);
        };

        trucks.forEach(truck => upsertMarker(truck.id, truck.name, truck.location));

        if (bounds.length > 0) {
            fleetMap.fitBounds(bounds, { padding: [24, 24] });
        }

        const pollLocations = () => {
            trucks.forEach(truck => {
                fetch(`/api/trucks/${truck.id}/location`)
                    .then(response => response.ok ? response.json() : null)
                    .then(data => {
                        if (data && data.current_location) {
                            upsertMarker(truck.id, truck.name, data.current_location);
                        }
                    })
                    .catch(() => {});
            });
        };

        setInterval(pollLocations, 30000);
    </script>
@endpush
