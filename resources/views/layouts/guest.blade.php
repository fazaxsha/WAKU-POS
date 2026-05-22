<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WAKU-POS') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        {{-- Fonts (preconnect + preload for faster LCP) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

        {{-- Bootstrap Icons --}}
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

        {{-- Vite --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-white">
        <div class="min-h-screen flex">
            <!-- Left Side: Form Area -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 lg:px-12 bg-white">
                <div class="w-full max-w-md">
                    <!-- Logo / Brand -->
                    <div class="mb-10 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-teal-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-teal-600/30">
                            🌤️
                        </div>
                        <div>
                            <div class="text-xl font-bold tracking-tight text-slate-900">{{ config('app.name', 'WAKU-POS') }}</div>
                            <div class="text-xs font-mono text-slate-500 uppercase tracking-widest">Retail Management</div>
                        </div>
                    </div>

                    <!-- Slot Content -->
                    {{ $slot }}
                </div>
            </div>

            <!-- Right Side: Brand Visual Area -->
            <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-slate-900 via-teal-950 to-slate-900 items-center justify-center p-12 relative overflow-hidden">
                {{-- Abstract Pattern Overlays --}}
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIiBmaWxsLW9wYWNpdHk9IjAuMSI+PC9yZWN0Pgo8cGF0aCBkPSJNMCAwTDggOFpNOCAwTDAgOFoiIHN0cm9rZT0iI2ZmZiIgc3Ryb2tlLW9wYWNpdHk9IjAuNSIgc3Ryb2tlLXdpZHRoPSIwLjUiPjwvcGF0aD4KPC9zdmc+')]"></div>
                <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-teal-500 blur-3xl opacity-20"></div>
                <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-cyan-400 blur-3xl opacity-20"></div>

                <div class="relative z-10 text-center max-w-lg">
                    <h2 class="text-3xl font-bold text-white mb-6 leading-tight">Selamat Datang di WAKU-POS 🌤️</h2>
                    <p class="text-teal-100 text-lg leading-relaxed">Standar baru untuk operasional toko Anda!</p>
                </div>  
            </div>
        </div>
    </body>
</html>
