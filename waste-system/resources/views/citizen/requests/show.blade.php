@extends('layouts.citizen')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Request #{{ $pickupRequest->id }}</h1>
        <p class="text-sm text-slate-600">Track your pickup status and segregation review.</p>
    </div>

    @php
        $steps = [
            'pending' => 'Submitted',
            'assigned' => 'Assigned to driver',
            'in_progress' => 'Driver en route',
            'completed' => 'Collected',
            'cancelled' => 'Cancelled',
        ];
        $currentStatus = $pickupRequest->status;
        $statusKeys = array_keys($steps);
        $currentIndex = array_search($currentStatus, $statusKeys, true);
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-lg font-semibold mb-4">Status Timeline</h2>
            <ol class="space-y-3">
                @foreach ($steps as $key => $label)
                    @php
                        $statusIndex = array_search($key, $statusKeys, true);
                        $isActive = $key === $currentStatus;
                        $isComplete = $statusIndex !== false && $currentIndex !== false && $statusIndex <= $currentIndex;
                    @endphp
                    <li class="flex items-center gap-3">
                        <span class="h-3 w-3 rounded-full {{ $isComplete ? 'bg-emerald-500' : 'bg-slate-200' }}"></span>
                        <div class="text-sm">
                            <div class="font-medium {{ $isActive ? 'text-emerald-700' : 'text-slate-700' }}">{{ $label }}</div>
                            @if ($isActive)
                                <div class="text-xs text-slate-500">Current status</div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6 space-y-3">
            <h2 class="text-lg font-semibold">Request Details</h2>
            <div class="text-sm text-slate-600">Waste type: <span class="capitalize text-slate-900">{{ $pickupRequest->waste_type }}</span></div>
            <div class="text-sm text-slate-600">Segregation: <span class="capitalize text-slate-900">{{ str_replace('_', ' ', $pickupRequest->segregation_status) }}</span></div>
            <div class="text-sm text-slate-600">Address: <span class="text-slate-900">{{ $pickupRequest->address }}</span></div>
            <div class="text-sm text-slate-600">Scheduled: <span class="text-slate-900">{{ optional($pickupRequest->scheduled_at)->format('d M Y, g:i a') ?? 'TBD' }}</span></div>
        </div>
    </div>
@endsection
