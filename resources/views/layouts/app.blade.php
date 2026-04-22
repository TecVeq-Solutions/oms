<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ app_setting('site_name', config('app.name', 'Office Suite')) }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        :root {
            --tec-primary: #0B1533;
            --tec-secondary: #1B2A57;
            --tec-accent: #2E3F7A;
            --tec-accent-soft: #EAF0FF;
            --tec-border: #DCE4F5;
            --tec-text: #17223b;
            --tec-muted: #6b7280;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(46, 63, 122, .10), transparent 28%),
                radial-gradient(circle at bottom right, rgba(11, 21, 51, .08), transparent 24%),
                linear-gradient(180deg, #F8FAFF 0%, #EEF3FF 100%);
            color: var(--tec-text);
        }

        .tec-main-grid {
            position: relative;
            min-height: 100vh;
        }

        .tec-main-grid::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(46, 63, 122, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(46, 63, 122, 0.04) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: .22;
        }

        .tec-page-wrap {
            position: relative;
            z-index: 1;
        }

        .tec-page-header-card,
        .tec-footer-card {
            background: rgba(255, 255, 255, .82);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(46, 63, 122, .10);
            box-shadow: 0 18px 45px rgba(11, 21, 51, .06);
        }

        .sidebar-scroll {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, .25) transparent;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .22);
            border-radius: 9999px;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex tec-main-grid">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col min-w-0 lg:ml-72 tec-page-wrap">
            {{-- Mobile Header --}}
            <div class="lg:hidden sticky top-0 z-30 px-4 py-3 bg-white/80 backdrop-blur border-b border-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" type="button"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-700 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="min-w-0 flex-1 text-center">
                        <h1 class="text-sm font-bold text-slate-800 truncate">
                            {{ app_setting('site_name', config('app.name', 'Office Suite')) }}
                        </h1>
                        <p class="text-[11px] text-slate-500 truncate">
                            {{ app_setting('site_tagline', 'Manage employees, attendance, CRM and campaigns') }}
                        </p>
                    </div>

                    <div class="w-11"></div>
                </div>
            </div>

            <div class="hidden lg:block">
                @include('layouts.topbar')
            </div>

            @if (isset($header))
                <header class="px-4 sm:px-6 lg:px-8 pt-6">
                    <div class="max-w-7xl mx-auto">
                        <div class="tec-page-header-card rounded-3xl px-4 sm:px-6 py-5">
                            {{ $header }}
                        </div>
                    </div>
                </header>
            @endif

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>

            <footer class="mt-auto px-4 sm:px-6 lg:px-8 pb-6">
                <div class="max-w-7xl mx-auto">
                    <div class="tec-footer-card rounded-3xl px-4 sm:px-5 py-4">
                        <div
                            class="flex flex-col md:flex-row items-center justify-between gap-2 text-sm text-slate-500 text-center md:text-left">
                            <p>{{ app_setting('footer_text', '© Office Management System. All rights reserved.') }}</p>
                            <p>{{ app_setting('site_tagline', 'Manage employees, attendance, CRM and campaigns') }}</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>

</html>