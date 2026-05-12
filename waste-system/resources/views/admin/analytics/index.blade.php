@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Analytics</h1>
        <p class="text-sm text-slate-600">Deep dive into collection performance and compliance.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Daily Requests (30 days)</h2>
            <canvas id="analytics-daily" height="140"></canvas>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Waste Type Breakdown</h2>
            <canvas id="analytics-waste" height="140"></canvas>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Compliance Rate by Zone</h2>
            <canvas id="analytics-zone" height="160"></canvas>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Truck Utilization</h2>
            <canvas id="analytics-truck" height="160"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const daily = @json($charts['daily_requests']);
        const waste = @json($charts['waste_breakdown']);
        const zones = @json($charts['zone_compliance']);
        const trucks = @json($charts['truck_utilization']);

        new Chart(document.getElementById('analytics-daily'), {
            type: 'line',
            data: {
                labels: daily.map(item => item.date),
                datasets: [{
                    label: 'Requests',
                    data: daily.map(item => item.count),
                    borderColor: '#14b8a6',
                    backgroundColor: 'rgba(20,184,166,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        new Chart(document.getElementById('analytics-waste'), {
            type: 'doughnut',
            data: {
                labels: waste.map(item => item.type),
                datasets: [{
                    data: waste.map(item => item.count),
                    backgroundColor: ['#22c55e', '#0ea5e9', '#f97316', '#ef4444']
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });

        new Chart(document.getElementById('analytics-zone'), {
            type: 'bar',
            data: {
                labels: zones.map(item => item.zone),
                datasets: [{
                    label: 'Compliance %',
                    data: zones.map(item => item.rate),
                    backgroundColor: '#16a34a'
                }]
            },
            options: { indexAxis: 'x', scales: { y: { beginAtZero: true, max: 100 } } }
        });

        new Chart(document.getElementById('analytics-truck'), {
            type: 'bar',
            data: {
                labels: trucks.map(item => item.truck),
                datasets: [{
                    label: 'Utilization %',
                    data: trucks.map(item => item.rate),
                    backgroundColor: '#0ea5e9'
                }]
            },
            options: { indexAxis: 'y', scales: { x: { beginAtZero: true, max: 100 } } }
        });
    </script>
@endpush
