@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Create Route</h1>
        <p class="text-sm text-slate-600">Assign a truck and order pickup stops.</p>
    </div>

    <form method="POST" action="{{ route('admin.routes.store') }}">
        @csrf
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm p-6 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium">Route Name</label>
                        <input class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Truck</label>
                        <select class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" name="truck_id" required>
                            <option value="">Select truck</option>
                            @foreach ($trucks as $truck)
                                <option value="{{ $truck->id }}" @selected(old('truck_id') == $truck->id)>{{ $truck->registration_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Scheduled Date</label>
                        <input class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" type="date" name="scheduled_date" value="{{ old('scheduled_date') }}" required>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Estimated Duration (mins)</label>
                        <input class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" type="number" min="10" name="estimated_duration_minutes" value="{{ old('estimated_duration_minutes', 90) }}" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Description</label>
                        <textarea class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" rows="3" name="description">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold mb-4">Stops</h2>
                    <div class="text-xs text-slate-500 mb-3">Assign an order number to include the stop in the route. Drag markers to fine tune positions.</div>
                    @error('stops')<p class="text-xs text-rose-600 mb-3">{{ $message }}</p>@enderror
                    <div class="max-h-96 overflow-y-auto border border-slate-200 rounded-xl">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500 sticky top-0">
                                <tr>
                                    <th class="text-left px-3 py-2">Address</th>
                                    <th class="text-left px-3 py-2">Waste</th>
                                    <th class="text-left px-3 py-2">Order</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($requests as $request)
                                    <tr>
                                        <td class="px-3 py-2">{{ $request->address }}</td>
                                        <td class="px-3 py-2 capitalize">{{ $request->waste_type }}</td>
                                        <td class="px-3 py-2">
                                            <input class="w-20 rounded-md border border-slate-200 px-2 py-1 text-sm" type="number" min="1" name="stops[{{ $request->id }}]" data-order-for="{{ $request->id }}">
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($requests->isEmpty())
                                    <tr>
                                        <td class="px-3 py-6 text-center text-slate-500" colspan="3">No pending pickup requests.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold mb-4">Route Map</h2>
                <div id="route-map" class="rounded-xl border border-slate-200"></div>
                <button class="mt-6 w-full rounded-md bg-slate-900 text-white py-2 text-sm">Create Route</button>
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #route-map {
            height: 520px;
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
        const requests = @json($requests->map(fn($request) => [
            'id' => (string) $request->id,
            'address' => $request->address,
            'waste_type' => $request->waste_type,
            'location' => $request->location,
        ])->values());

        const map = L.map('route-map').setView([28.6139, 77.2090], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = {};

        const labelFor = (order) => order ? `#${order}` : '';

        const bounds = [];

        requests.forEach(request => {
            if (!request.location || !request.location.coordinates) {
                return;
            }
            const lat = request.location.coordinates[1];
            const lng = request.location.coordinates[0];
            const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.bindTooltip(labelFor(''), { permanent: true, className: 'marker-label' });
            marker.bindPopup(`${request.address} (${request.waste_type})`);
            markers[request.id] = marker;
            bounds.push([lat, lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [24, 24] });
        }

        document.querySelectorAll('[data-order-for]').forEach(input => {
            input.addEventListener('input', event => {
                const id = event.target.getAttribute('data-order-for');
                const marker = markers[id];
                if (marker) {
                    marker.setTooltipContent(labelFor(event.target.value));
                }
            });
        });
    </script>
@endpush
