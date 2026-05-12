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
                    <div class="text-lg font-semibold tracking-wide">Smart Waste System</div>
                    <nav class="flex items-center gap-4 text-sm">
                        @if (Route::has('login'))
                            @auth
                                <a class="px-4 py-2 rounded-full border border-white/20 hover:border-white/60" href="{{ url('/dashboard') }}">Dashboard</a>
                            @else
                                <a class="px-4 py-2 rounded-full border border-white/20 hover:border-white/60" href="{{ route('login') }}">Log in</a>
                                @if (Route::has('register'))
                                    <a class="px-4 py-2 rounded-full bg-emerald-400 text-slate-950" href="{{ route('register') }}">Register</a>
                                @endif
                            @endauth
                        @endif
                    </nav>
                </div>
            </header>

            <main class="relative z-10">
                <div class="max-w-6xl mx-auto px-6 py-16 lg:py-24 grid gap-12 lg:grid-cols-2">
                    <div class="space-y-6 fade-up">
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-200">Local first operations</p>
                        <h1 class="text-4xl lg:text-5xl font-semibold leading-tight">Route, collect, and analyze waste without leaving your local network.</h1>
                        <p class="text-base text-slate-200 max-w-xl">Run a full smart waste segregation and collection workflow using Laravel 11, MongoDB 7, Leaflet, and Chart.js. Citizen requests, driver routes, and admin analytics stay in one local system.</p>
                        <div class="flex flex-wrap gap-3">
                            <a class="px-5 py-3 rounded-full bg-emerald-400 text-slate-950 text-sm" href="{{ route('register') }}">Start a request</a>
                            <a class="px-5 py-3 rounded-full border border-white/30 text-sm" href="{{ route('login') }}">Admin console</a>
                        </div>
                        <div class="flex items-center gap-6 text-xs text-slate-300">
                            <span>MongoDB geo queries</span>
                            <span>Queue jobs</span>
                            <span>Live fleet maps</span>
                        </div>
                    </div>

                    <div class="grid gap-4 fade-up-delay-1">
                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur">
                            <p class="text-xs uppercase tracking-wide text-emerald-200">Citizen flow</p>
                            <h2 class="text-lg font-semibold mt-2">Request pickups with geo pins</h2>
                            <p class="text-sm text-slate-200 mt-2">Residents drop a pin, select waste type, and track segregation compliance in real time.</p>
                        </div>
                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur">
                            <p class="text-xs uppercase tracking-wide text-sky-200">Driver flow</p>
                            <h2 class="text-lg font-semibold mt-2">Turn stops into a clean route</h2>
                            <p class="text-sm text-slate-200 mt-2">Drivers see ordered stops, log collected weight, and flag segregation violations.</p>
                        </div>
                        <div class="bg-white/10 border border-white/10 rounded-2xl p-6 backdrop-blur">
                            <p class="text-xs uppercase tracking-wide text-amber-200">Admin flow</p>
                            <h2 class="text-lg font-semibold mt-2">Measure compliance and utilization</h2>
                            <p class="text-sm text-slate-200 mt-2">Track daily volume, waste mix, zone compliance, and truck utilization with live charts.</p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
