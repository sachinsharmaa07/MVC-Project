@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Pickup Requests</h1>
        <p class="text-sm text-slate-600">Filter by waste type, status, and date.</p>
    </div>

    <form class="bg-white rounded-2xl shadow-sm p-4 mb-6 grid gap-4 md:grid-cols-4" method="GET">
        <div>
            <label class="text-xs font-medium text-slate-500">Waste Type</label>
            <select class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm" name="waste_type">
                <option value="">All</option>
                @foreach (['dry','wet','hazardous','mixed'] as $type)
                    <option value="{{ $type }}" @selected(request('waste_type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Status</label>
            <select class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm" name="status">
                <option value="">All</option>
                @foreach (['pending','assigned','in_progress','completed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Date</label>
            <input class="mt-1 w-full rounded-md border border-slate-200 px-3 py-2 text-sm" type="date" name="date" value="{{ request('date') }}">
        </div>
        <div class="flex items-end">
            <button class="w-full rounded-md bg-slate-900 text-white py-2 text-sm">Apply Filters</button>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left px-4 py-3">Request ID</th>
                    <th class="text-left px-4 py-3">Citizen</th>
                    <th class="text-left px-4 py-3">Waste Type</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Segregation</th>
                    <th class="text-left px-4 py-3">Address</th>
                    <th class="text-left px-4 py-3">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($requests as $request)
                    <tr>
                        <td class="px-4 py-3">#{{ $request->id }}</td>
                        <td class="px-4 py-3">{{ $request->citizen_id }}</td>
                        <td class="px-4 py-3 capitalize">{{ $request->waste_type }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $request->status) }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $request->segregation_status) }}</td>
                        <td class="px-4 py-3">{{ $request->address }}</td>
                        <td class="px-4 py-3">{{ optional($request->created_at)->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-500" colspan="7">No requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
