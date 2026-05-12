@extends('layouts.admin')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">Routes</h1>
            <p class="text-sm text-slate-600">Planned and active routes for the fleet.</p>
        </div>
        <a class="rounded-md bg-slate-900 text-white px-4 py-2 text-sm" href="{{ route('admin.routes.create') }}">Create Route</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left px-4 py-3">Route</th>
                    <th class="text-left px-4 py-3">Truck</th>
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
                        <td class="px-4 py-3">{{ $route->truck?->registration_number ?? $route->truck_id }}</td>
                        <td class="px-4 py-3">{{ optional($route->scheduled_date)->format('d M Y') }}</td>
                        <td class="px-4 py-3 capitalize">{{ $route->status }}</td>
                        <td class="px-4 py-3">{{ is_array($route->stops) ? count($route->stops) : 0 }}</td>
                        <td class="px-4 py-3 text-right">
                            <a class="text-slate-900" href="{{ route('admin.routes.show', $route->id) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-500" colspan="6">No routes created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
