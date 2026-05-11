@extends('layouts.citizen')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">My Pickup Requests</h1>
        <a class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md text-sm" href="{{ route('citizen.requests.create') }}">
            New Request
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50 text-emerald-700">
                <tr>
                    <th class="text-left px-4 py-3">Request</th>
                    <th class="text-left px-4 py-3">Waste Type</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Created</th>
                    <th class="text-left px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($requests as $request)
                    <tr>
                        <td class="px-4 py-3">#{{ $request->id }}</td>
                        <td class="px-4 py-3 capitalize">{{ $request->waste_type }}</td>
                        <td class="px-4 py-3 capitalize">{{ str_replace('_', ' ', $request->status) }}</td>
                        <td class="px-4 py-3">{{ optional($request->created_at)->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a class="text-emerald-700" href="{{ route('citizen.requests.show', $request->id) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-500" colspan="5">No pickup requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
