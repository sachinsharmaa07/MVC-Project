@extends('layouts.driver')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Log Collection</h1>
        <p class="text-sm text-slate-600">Request #{{ $pickupRequest->id }} · {{ $pickupRequest->address }}</p>
    </div>

    <form method="POST" action="{{ route('driver.collect', $pickupRequest->id) }}" class="bg-white rounded-2xl shadow-sm p-6 max-w-xl">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="text-sm font-medium">Collected Weight (kg)</label>
                <input class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" type="number" step="0.1" min="0.1" name="collected_weight_kg" value="{{ old('collected_weight_kg') }}" required>
                @error('collected_weight_kg')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">Segregation Compliant</label>
                <select class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" name="segregation_compliant" required>
                    <option value="1" @selected(old('segregation_compliant', '1') === '1')>Yes</option>
                    <option value="0" @selected(old('segregation_compliant') === '0')>No</option>
                </select>
                @error('segregation_compliant')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="text-sm font-medium">Driver Notes</label>
                <textarea class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2" rows="3" name="driver_notes">{{ old('driver_notes') }}</textarea>
                @error('driver_notes')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>
            <button class="w-full rounded-md bg-blue-600 text-white py-2 text-sm">Submit Log</button>
        </div>
    </form>
@endsection
