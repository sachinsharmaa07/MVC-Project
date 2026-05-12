<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Waste System') }} - Citizen</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=sora:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-['Sora'] antialiased bg-gradient-to-br from-emerald-50 via-white to-lime-50 text-slate-900">
        <div class="min-h-screen">
            <header class="bg-white shadow-sm">
                <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div>
                        <div class="text-lg font-semibold">Waste Citizen Portal</div>
                        <div class="text-xs text-slate-500">Request pickups and track status</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a class="text-sm text-emerald-700" href="{{ route('citizen.requests.create') }}">New Request</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm text-slate-500 hover:text-slate-900" type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="max-w-5xl mx-auto px-6 py-6">
                @if (session('status'))
                    <div class="mb-4 rounded-md bg-emerald-100 text-emerald-700 px-4 py-2 text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
