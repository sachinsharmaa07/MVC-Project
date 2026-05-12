<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Waste System') }} - Admin</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=sora:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-['Sora'] antialiased bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-slate-100">
        <div class="min-h-screen flex">
            <aside class="w-64 bg-slate-950/80 text-slate-100 p-6 flex flex-col border-r border-white/10">
                <div class="text-xl font-semibold tracking-wide">Waste Admin</div>
                <nav class="mt-8 space-y-2 text-sm">
                    <a class="block px-3 py-2 rounded-md hover:bg-slate-800/70" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-slate-800/70" href="{{ route('admin.requests') }}">Requests</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-slate-800/70" href="{{ route('admin.routes.index') }}">Routes</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-slate-800/70" href="{{ route('admin.analytics') }}">Analytics</a>
                    <a class="block px-3 py-2 rounded-md hover:bg-slate-800/70" href="{{ route('admin.export.csv') }}">Export CSV</a>
                </nav>
            </aside>

            <div class="flex-1 flex flex-col bg-gradient-to-br from-slate-50 via-white to-emerald-50 text-slate-900">
                <header class="bg-white/80 backdrop-blur shadow-sm">
                    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                        <div class="text-sm text-slate-500">Smart Waste Segregation & Collection</div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm">{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="text-sm text-slate-500 hover:text-slate-900" type="submit">Logout</button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="flex-1 max-w-6xl mx-auto w-full px-6 py-6">
                    @if (session('status'))
                        <div class="mb-4 rounded-md bg-emerald-100 text-emerald-700 px-4 py-2 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
