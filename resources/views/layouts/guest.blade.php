<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
   <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        .tec-bg {
            background: linear-gradient(135deg, #0B1533, #1B2A57, #2E3F7A);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="antialiased">

    <div class="min-h-screen flex items-center justify-center tec-bg px-4">

        <!-- Main Card Container -->
        <div class="w-full max-w-md">

            

            <!-- Login Card -->
            <div class="">
                {{ $slot }}
            </div>

        </div>
    </div>

</body>
</html>