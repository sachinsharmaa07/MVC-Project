<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Waste System') }} - Driver</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-blue-50 text-slate-900">
        <div class="min-h-screen">
            <header class="bg-white shadow-sm">
                <div class="max-w-5xl mx-auto px-6 py-4 flex items-center justify-between">
                    <div>
                        <div class="text-lg font-semibold">Driver Console</div>
                        <div class="text-xs text-slate-500">Today's routes and collection logs</div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a class="text-sm text-blue-700" href="{{ route('driver.routes.today') }}">Today's Routes</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="text-sm text-slate-500 hover:text-slate-900" type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="max-w-5xl mx-auto px-6 py-6">
                @if (session('status'))
                    <div class="mb-4 rounded-md bg-blue-100 text-blue-700 px-4 py-2 text-sm">
                        {{ session('status') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
