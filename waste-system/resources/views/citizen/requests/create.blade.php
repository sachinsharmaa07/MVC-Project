@extends('layouts.citizen')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Request a Pickup</h1>
        <p class="text-sm text-slate-600">Pin your location and submit your waste type for collection.</p>
    </div>

    <form method="POST" action="{{ route('citizen.requests.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <div>
                    <label class="text-sm font-medium">Pickup Address</label>
                    <input class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" name="address" value="{{ old('address') }}" required>
                    @error('address')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Waste Type</label>
                    <select class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" name="waste_type" required>
                        <option value="">Select waste type</option>
                        @foreach (['dry', 'wet', 'hazardous', 'mixed'] as $type)
                            <option value="{{ $type }}" @selected(old('waste_type') === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    @error('waste_type')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Notes (optional)</label>
                    <textarea class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" rows="4" name="notes">{{ old('notes') }}</textarea>
                    @error('notes')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-sm font-medium">Upload Photo (optional)</label>
                    <input class="mt-1 w-full text-sm" type="file" name="photo" accept="image/png,image/jpeg">
                    @error('photo')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span>Latitude: <span id="lat-readout">{{ old('latitude') ?? '--' }}</span></span>
                    <span>Longitude: <span id="lng-readout">{{ old('longitude') ?? '--' }}</span></span>
                </div>

                <button class="w-full rounded-md bg-emerald-600 text-white py-2 text-sm">Submit Request</button>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="mb-3">
                    <h2 class="text-lg font-semibold">Pick a Location</h2>
                    <p class="text-xs text-slate-500">Click on the map to set your pickup point.</p>
                </div>
                <div id="picker-map" class="rounded-xl border border-slate-200"></div>
                @error('latitude')<p class="text-xs text-rose-600 mt-2">{{ $message }}</p>@enderror
                @error('longitude')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </form>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #picker-map {
            height: 360px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const map = L.map('picker-map').setView([28.6139, 77.2090], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = null;
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const latReadout = document.getElementById('lat-readout');
        const lngReadout = document.getElementById('lng-readout');

        const setMarker = (lat, lng) => {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }

            latInput.value = lat;
            lngInput.value = lng;
            latReadout.textContent = lat.toFixed(6);
            lngReadout.textContent = lng.toFixed(6);
        };

        map.on('click', (event) => {
            setMarker(event.latlng.lat, event.latlng.lng);
        });

        if (latInput.value && lngInput.value) {
            setMarker(parseFloat(latInput.value), parseFloat(lngInput.value));
        }
    </script>
@endpush
