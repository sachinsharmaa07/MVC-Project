<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Waste System') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=sora:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-['Sora'] antialiased bg-slate-950 text-slate-100">
        <div class="min-h-screen relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.35),_transparent_60%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_70%,_rgba(14,165,233,0.25),_transparent_60%)]"></div>

            <header class="relative z-10">
                <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
                    <div class="text-lg font-semibold tracking-wide">♻️ Smart Waste System</div>
                    <nav class="flex items-center gap-4 text-sm">
                        @auth
                            <a href="{{ url('/profile') }}" class="px-4 py-2 rounded-full border border-white/20 hover:border-white/60">
                                {{ auth()->user()->name }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-full bg-red-500/20 border border-red-500/30 hover:bg-red-500/30">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a class="px-4 py-2 rounded-full border border-white/20 hover:border-white/60" href="{{ route('login') }}">Log in</a>
                            @if (Route::has('register'))
                                <a class="px-4 py-2 rounded-full bg-emerald-400 text-slate-950" href="{{ route('register') }}">Register</a>
                            @endif
                        @endauth
                    </nav>
                </div>
            </header>

            <main class="relative z-10">
                @auth
                    {{-- Authenticated User Dashboard Links --}}
                    <div class="max-w-6xl mx-auto px-6 py-16">
                        <div class="space-y-6 mb-12">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Welcome back, {{ auth()->user()->name }}</p>
                            <h1 class="text-4xl lg:text-5xl font-semibold leading-tight">
                                @if (auth()->user()->hasRole('citizen'))
                                    Request waste pickups
                                @elseif (auth()->user()->hasRole('driver'))
                                    Complete your routes
                                @else
                                    Manage the waste network
                                @endif
                            </h1>
                            <p class="text-base text-slate-200 max-w-xl">
                                @if (auth()->user()->hasRole('citizen'))
                                    Create pickup requests with geo pins, track collection status, and view compliance reports.
                                @elseif (auth()->user()->hasRole('driver'))
                                    Access today's assigned routes, navigate to stops, and log waste collections with photos.
                                @else
                                    Assign trucks and routes, review analytics, and manage compliance across your entire fleet.
                                @endif
                            </p>
                        </div>

                        @if (auth()->user()->hasRole('citizen'))
                            {{-- Citizen Quick Links --}}
                            <div class="grid md:grid-cols-3 gap-6">
                                <a href="{{ route('citizen.requests.create') }}" class="group bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 border border-emerald-500/30 rounded-2xl p-8 hover:border-emerald-500/60 transition">
                                    <div class="text-3xl mb-4">📍</div>
                                    <h2 class="text-xl font-semibold mb-2 group-hover:text-emerald-300 transition">New Pickup Request</h2>
                                    <p class="text-sm text-slate-200">Drop a pin and request waste collection from your location.</p>
                                </a>

                                <a href="{{ route('citizen.dashboard') }}" class="group bg-gradient-to-br from-sky-500/20 to-sky-500/5 border border-sky-500/30 rounded-2xl p-8 hover:border-sky-500/60 transition">
                                    <div class="text-3xl mb-4">📋</div>
                                    <h2 class="text-xl font-semibold mb-2 group-hover:text-sky-300 transition">Your Requests</h2>
                                    <p class="text-sm text-slate-200">View all your requests and track collection status in real time.</p>
                                </a>

                                <a href="{{ route('profile.edit') }}" class="group bg-gradient-to-br from-purple-500/20 to-purple-500/5 border border-purple-500/30 rounded-2xl p-8 hover:border-purple-500/60 transition">
                                    <div class="text-3xl mb-4">⚙️</div>
                                    <h2 class="text-xl font-semibold mb-2 group-hover:text-purple-300 transition">Account Settings</h2>
                                    <p class="text-sm text-slate-200">Update your profile and contact information.</p>
                                </a>
                            </div>

                        @elseif (auth()->user()->hasRole('driver'))
                            {{-- Driver Quick Links --}}
                            <div class="grid md:grid-cols-3 gap-6">
                                <a href="{{ route('driver.routes.today') }}" class="group bg-gradient-to-br from-amber-500/20 to-amber-500/5 border border-amber-500/30 rounded-2xl p-8 hover:border-amber-500/60 transition">
                                    <div class="text-3xl mb-4">🚚</div>
                                    <h2 class="text-xl font-semibold mb-2 group-hover:text-amber-300 transition">Today's Routes</h2>
                                    <p class="text-sm text-slate-200">View all routes assigned to you for today with stops and navigation.</p>
                                </a>

                                <a href="{{ route('driver.routes.today') }}#notifications" class="group bg-gradient-to-br from-blue-500/20 to-blue-500/5 border border-blue-500/30 rounded-2xl p-8 hover:border-blue-500/60 transition">
                                    <div class="text-3xl mb-4">🔔</div>
                                    <h2 class="text-xl font-semibold mb-2 group-hover:text-blue-300 transition">Notifications</h2>
                                    @php
                                        $notificationCount = auth()->user()->unreadNotifications->count();
                                    @endphp
                                    <p class="text-sm text-slate-200">
                                        @if ($notificationCount > 0)
                                            You have <span class="font-semibold text-blue-300">{{ $notificationCount }}</span> unread notifications.
                                        @else
                                            No new notifications.
                                        @endif
                                    </p>
                                </a>

                                <a href="{{ route('profile.edit') }}" class="group bg-gradient-to-br from-purple-500/20 to-purple-500/5 border border-purple-500/30 rounded-2xl p-8 hover:border-purple-500/60 transition">
                                    <div class="text-3xl mb-4">⚙️</div>
                                    <h2 class="text-xl font-semibold mb-2 group-hover:text-purple-300 transition">Account Settings</h2>
                                    <p class="text-sm text-slate-200">Update your profile and API tokens.</p>
                                </a>
                            </div>

                        @else
                            {{-- Admin Quick Links --}}
                            <div class="grid md:grid-cols-4 gap-4 mb-8">
                                <a href="{{ route('admin.dashboard') }}" class="group bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 border border-emerald-500/30 rounded-2xl p-6 hover:border-emerald-500/60 transition">
                                    <div class="text-2xl mb-3">📊</div>
                                    <h3 class="text-lg font-semibold mb-1 group-hover:text-emerald-300 transition">Dashboard</h3>
                                    <p class="text-xs text-slate-300">Real-time metrics</p>
                                </a>

                                <a href="{{ route('admin.requests') }}" class="group bg-gradient-to-br from-sky-500/20 to-sky-500/5 border border-sky-500/30 rounded-2xl p-6 hover:border-sky-500/60 transition">
                                    <div class="text-2xl mb-3">📋</div>
                                    <h3 class="text-lg font-semibold mb-1 group-hover:text-sky-300 transition">All Requests</h3>
                                    <p class="text-xs text-slate-300">Manage pickups</p>
                                </a>

                                <a href="{{ route('admin.routes.index') }}" class="group bg-gradient-to-br from-amber-500/20 to-amber-500/5 border border-amber-500/30 rounded-2xl p-6 hover:border-amber-500/60 transition">
                                    <div class="text-2xl mb-3">🛣️</div>
                                    <h3 class="text-lg font-semibold mb-1 group-hover:text-amber-300 transition">Routes</h3>
                                    <p class="text-xs text-slate-300">Create & assign</p>
                                </a>

                                <a href="{{ route('admin.analytics') }}" class="group bg-gradient-to-br from-purple-500/20 to-purple-500/5 border border-purple-500/30 rounded-2xl p-6 hover:border-purple-500/60 transition">
                                    <div class="text-2xl mb-3">📈</div>
                                    <h3 class="text-lg font-semibold mb-1 group-hover:text-purple-300 transition">Analytics</h3>
                                    <p class="text-xs text-slate-300">Compliance & stats</p>
                                </a>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <a href="{{ route('admin.export.csv') }}" class="group bg-gradient-to-br from-pink-500/20 to-pink-500/5 border border-pink-500/30 rounded-2xl p-6 hover:border-pink-500/60 transition">
                                    <div class="text-2xl mb-3">📥</div>
                                    <h3 class="text-lg font-semibold mb-1 group-hover:text-pink-300 transition">Export Report</h3>
                                    <p class="text-xs text-slate-300">Download CSV data</p>
                                </a>

                                <a href="{{ route('profile.edit') }}" class="group bg-gradient-to-br from-indigo-500/20 to-indigo-500/5 border border-indigo-500/30 rounded-2xl p-6 hover:border-indigo-500/60 transition">
                                    <div class="text-2xl mb-3">⚙️</div>
                                    <h3 class="text-lg font-semibold mb-1 group-hover:text-indigo-300 transition">Settings</h3>
                                    <p class="text-xs text-slate-300">Manage account</p>
                                </a>
                            </div>
                        @endif

                        {{-- System Stats Footer --}}
                        <div class="mt-12 pt-8 border-t border-white/10">
                            <div class="grid md:grid-cols-4 gap-6 text-center">
                                @php
                                    $totalRequests = \App\Models\PickupRequest::count();
                                    $totalRoutes = \App\Models\Route::count();
                                    $totalTrucks = \App\Models\Truck::count();
                                    $complianceRate = $totalRequests > 0 ? round((\App\Models\PickupRequest::where('segregation_status', 'compliant')->count() / $totalRequests) * 100, 1) : 0;
                                @endphp
                                <div>
                                    <div class="text-3xl font-semibold text-emerald-400">{{ $totalRequests }}</div>
                                    <p class="text-xs text-slate-400">Total Requests</p>
                                </div>
                                <div>
                                    <div class="text-3xl font-semibold text-amber-400">{{ $totalRoutes }}</div>
                                    <p class="text-xs text-slate-400">Active Routes</p>
                                </div>
                                <div>
                                    <div class="text-3xl font-semibold text-blue-400">{{ $totalTrucks }}</div>
                                    <p class="text-xs text-slate-400">Fleet Trucks</p>
                                </div>
                                <div>
                                    <div class="text-3xl font-semibold text-purple-400">{{ $complianceRate }}%</div>
                                    <p class="text-xs text-slate-400">Compliance Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Unauthenticated User Landing Page --}}
                    <div class="max-w-6xl mx-auto px-6 py-16 lg:py-24 grid gap-12 lg:grid-cols-2">
                        <div class="space-y-6 fade-up">
                            <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Local first operations</p>
                            <h1 class="text-4xl lg:text-5xl font-semibold leading-tight">Route, collect, and analyze waste without leaving your local network.</h1>
                            <p class="text-base text-slate-200 max-w-xl">Run a full smart waste segregation and collection workflow using Laravel 11, MongoDB 7, Leaflet, and Chart.js. Citizen requests, driver routes, and admin analytics stay in one local system.</p>
                            <div class="flex flex-wrap gap-3">
                                <a class="px-5 py-3 rounded-full bg-emerald-400 text-slate-950 text-sm font-semibold hover:bg-emerald-300 transition" href="{{ route('register') }}">Start a request</a>
                                <a class="px-5 py-3 rounded-full border border-white/30 text-sm hover:border-white/60 transition" href="{{ route('login') }}">Admin console</a>
                            </div>
                            <div class="flex items-center gap-6 text-xs text-slate-300">
                                <span>MongoDB geo queries</span>
                                <span>Queue jobs</span>
                                <span>Live fleet maps</span>
                            </div>
                        </div>

                        <div class="grid gap-4 fade-up-delay-1">
                            <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur hover:bg-white/15 transition">
                                <p class="text-xs uppercase tracking-wide text-emerald-200">📱 Citizen flow</p>
                                <h2 class="text-lg font-semibold mt-2">Request pickups with geo pins</h2>
                                <p class="text-sm text-slate-200 mt-2">Residents drop a pin, select waste type, and track segregation compliance in real time.</p>
                            </div>
                            <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur hover:bg-white/15 transition">
                                <p class="text-xs uppercase tracking-wide text-sky-200">🚚 Driver flow</p>
                                <h2 class="text-lg font-semibold mt-2">Turn stops into a clean route</h2>
                                <p class="text-sm text-slate-200 mt-2">Drivers see ordered stops, log collected weight, and flag segregation violations.</p>
                            </div>
                            <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur hover:bg-white/15 transition">
                                <p class="text-xs uppercase tracking-wide text-amber-200">📊 Admin flow</p>
                                <h2 class="text-lg font-semibold mt-2">Measure compliance and utilization</h2>
                                <p class="text-sm text-slate-200 mt-2">Track daily volume, waste mix, zone compliance, and truck utilization with live charts.</p>
                            </div>
                        </div>
                    </div>
                @endauth
            </main>
        </div>
    </body>
</html>
