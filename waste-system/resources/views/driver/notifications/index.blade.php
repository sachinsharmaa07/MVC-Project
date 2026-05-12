@extends('layouts.driver')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white">Notifications</h1>
        @if (auth()->user()->unreadNotifications->count() > 0)
            <form action="{{ route('driver.notifications.mark-all-read') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    @if ($notifications->count() > 0)
        <div class="space-y-4">
            @foreach ($notifications as $notification)
                <a href="{{ route('driver.notifications.show', $notification->id) }}" class="block bg-slate-800 border-l-4 border-emerald-500 hover:bg-slate-700 transition rounded p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h2 class="text-lg font-semibold text-white">
                                    @if ($notification->type === 'App\Notifications\RouteAssigned')
                                        🚚 New Route Assigned
                                    @else
                                        📬 {{ class_basename($notification->type) }}
                                    @endif
                                </h2>
                                @if (!$notification->read_at)
                                    <span class="inline-block w-2 h-2 bg-emerald-500 rounded-full"></span>
                                @endif
                            </div>
                            <p class="text-slate-300 text-sm">
                                @if ($notification->type === 'App\Notifications\RouteAssigned')
                                    Route assigned for {{ \Carbon\Carbon::parse($notification->data['scheduled_date'])->format('M d, Y') }} with {{ $notification->data['stop_count'] }} stops
                                @else
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-slate-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-5xl mb-4">📭</div>
            <h2 class="text-xl font-semibold text-white mb-2">No Notifications</h2>
            <p class="text-slate-400">You're all caught up! Check back later for updates.</p>
        </div>
    @endif
</div>
@endsection
