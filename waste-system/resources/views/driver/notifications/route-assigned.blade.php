@extends('layouts.driver')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('driver.routes.today') }}" class="text-emerald-400 hover:text-emerald-300 text-sm">← Back to Routes</a>
    </div>

    <div class="bg-slate-800 border border-slate-700 rounded-lg p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">New Route Assigned</h1>
                <p class="text-slate-300">Route ID: {{ $notification['data']['route_id'] }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-slate-400">Assigned on</div>
                <div class="text-lg font-semibold text-emerald-400">
                    {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y') }}
                </div>
            </div>
        </div>

        <div class="border-t border-slate-700 pt-6">
            <h2 class="text-xl font-semibold text-white mb-4">Route Details</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-slate-700/50 rounded p-4">
                    <div class="text-sm text-slate-400">Scheduled Date</div>
                    <div class="text-2xl font-bold text-emerald-400">
                        {{ \Carbon\Carbon::parse($notification['data']['scheduled_date'])->format('M d, Y') }}
                    </div>
                </div>
                <div class="bg-slate-700/50 rounded p-4">
                    <div class="text-sm text-slate-400">Estimated Duration</div>
                    <div class="text-2xl font-bold text-amber-400">
                        {{ $notification['data']['estimated_duration_minutes'] }} min
                    </div>
                </div>
                <div class="bg-slate-700/50 rounded p-4">
                    <div class="text-sm text-slate-400">Number of Stops</div>
                    <div class="text-2xl font-bold text-sky-400">
                        {{ $notification['data']['stop_count'] }}
                    </div>
                </div>
                <div class="bg-slate-700/50 rounded p-4">
                    <div class="text-sm text-slate-400">Status</div>
                    <div class="text-2xl font-bold text-purple-400">
                        Planned
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-700 mt-6 pt-6">
            <h2 class="text-xl font-semibold text-white mb-4">Next Steps</h2>
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-500/20 border border-emerald-500">
                            <span class="text-emerald-400 font-bold">1</span>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-white">Review Route Details</p>
                        <p class="text-slate-300 text-sm">Check the scheduled date and estimated duration to plan your day.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-500/20 border border-emerald-500">
                            <span class="text-emerald-400 font-bold">2</span>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-white">View All Stops</p>
                        <p class="text-slate-300 text-sm">Go to "Today's Routes" to see the ordered list of pickup locations.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-emerald-500/20 border border-emerald-500">
                            <span class="text-emerald-400 font-bold">3</span>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-white">Log Collections</p>
                        <p class="text-slate-300 text-sm">As you complete each stop, log the weight and photo of waste collected.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-700 mt-6 pt-6">
            <a href="{{ route('driver.routes.today') }}" class="inline-block px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition">
                View Today's Routes
            </a>
        </div>
    </div>

    {{-- Mark notification as read --}}
    @if (!$notification->read_at)
        <script>
            fetch('{{ route("notifications.mark-read", $notification->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        </script>
    @endif
</div>
@endsection
